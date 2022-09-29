<?php

namespace WPStaging\Pro\Backup;

use WPStaging\Core\WPStaging;
use WPStaging\Framework\Facades\Sanitize;
use WPStaging\Framework\Security\Capabilities;
use WPStaging\Framework\Security\Nonce;
use WPStaging\Pro\Backup\BackgroundProcessing\Export\PrepareExport;
use WPStaging\Pro\Backup\Dto\Job\JobExportDataDto;
use WPStaging\Pro\Backup\Service\BackupsFinder;

use function WPStaging\functions\debug_log;

class BackupScheduler
{
    const BACKUP_SCHEDULE_ERROR_REPORT_OPTION = 'wpstg_backup_schedules_send_error_report';

    const BACKUP_SCHEDULE_REPORT_EMAIL_OPTION = 'wpstg_backup_schedules_report_email';

    protected $backupsFinder;

    protected $processLock;

    /**
     * Store cron related message
     * @var string
     */
    protected $cronMessage;

    public function __construct(BackupsFinder $backupsFinder, BackupProcessLock $processLock)
    {
        $this->backupsFinder = $backupsFinder;
        $this->processLock = $processLock;
    }

    const OPTION_BACKUP_SCHEDULES = 'wpstg_backup_schedules';

    /**
     * @return false|mixed|void
     */
    public function getSchedules()
    {
        return get_option(static::OPTION_BACKUP_SCHEDULES, []);
    }

    /**
     * @param JobExportDataDto $jobExportDataDto
     * @return void
     */
    public function maybeDeleteOldBackups(JobExportDataDto $jobExportDataDto)
    {
        $scheduleId = $jobExportDataDto->getScheduleId();

        // Not a scheduled backup, nothing to do.
        if (empty($scheduleId)) {
            return;
        }

        $schedules = get_option(static::OPTION_BACKUP_SCHEDULES, []);

        $schedule = array_filter($schedules, function ($schedule) use ($scheduleId) {
            return $schedule['scheduleId'] == $scheduleId;
        });

        if (empty($schedule)) {
            debug_log("Could not delete old backups for schedule ID $scheduleId as the schedule rotation plan was not found in the database.");

            return;
        }

        $schedule = array_shift($schedule);

        $rotation = absint($schedule['rotation']);

        $backupFiles = $this->backupsFinder->findBackupByScheduleId($scheduleId);

        // Early bail: Not enough backups to trigger the rotation
        if (count($backupFiles) < $rotation) {
            return;
        }

        // Sort backups, older first
        uasort($backupFiles, function ($backup1, $backup2) {
            /**
             * @var \SplFileInfo $backup1
             * @var \SplFileInfo $backup2
             */
            if ($backup1->getMTime() === $backup2->getMTime()) {
                return 0;
            }

            return $backup1->getMTime() < $backup2->getMTime() ? -1 : 1;
        });

        // Make sure array indexes are correctly ordered.
        $backupFiles = array_values($backupFiles);

        // Get exceeding backups, including an extra one for the backup that will be created right now.
        $backupFiles = array_slice($backupFiles, 0, max(1, count($backupFiles) - $rotation + 1));

        array_map(function ($file) {
            /**
             * @var \SplFileInfo $file
             */
            if (!unlink($file->getPathname())) {
                debug_log('Tried to cleanup old backups for backup plan rotation, but couldnt delete file: ' . $file->getPathname());
            }
        }, $backupFiles);
    }

    public function scheduleBackup(JobExportDataDto $jobExportDataDto, $scheduleId)
    {
        if (!isset(wp_get_schedules()[$jobExportDataDto->getScheduleRecurrence()])) {
            \WPStaging\functions\debug_log("Tried to schedule a backup, but schedule '" . $jobExportDataDto->getScheduleRecurrence() . "' is not registered as a WordPress cron schedule. Data DTO: " . wp_json_encode($jobExportDataDto));

            return;
        }

        $firstSchedule = new \DateTime('now', wp_timezone());
        $time = $jobExportDataDto->getScheduleTime();
        $this->setUpcomingDateTime($firstSchedule, $time);

        $backupSchedule = [
            'scheduleId' => $scheduleId,
            'schedule' => $jobExportDataDto->getScheduleRecurrence(),
            'time' => $time,
            'name' => $jobExportDataDto->getName(),
            'rotation' => $jobExportDataDto->getScheduleRotation(),
            'isExportingPlugins' => $jobExportDataDto->getIsExportingPlugins(),
            'isExportingMuPlugins' => $jobExportDataDto->getIsExportingMuPlugins(),
            'isExportingThemes' => $jobExportDataDto->getIsExportingThemes(),
            'isExportingUploads' => $jobExportDataDto->getIsExportingUploads(),
            'isExportingOtherWpContentFiles' => $jobExportDataDto->getIsExportingOtherWpContentFiles(),
            'isExportingDatabase' => $jobExportDataDto->getIsExportingDatabase(),
            'sitesToExport' => $jobExportDataDto->getSitesToExport(),
            'storages' => $jobExportDataDto->getStorages(),
            'firstSchedule' => $firstSchedule->getTimestamp(),
        ];

        if (wp_next_scheduled('wpstg_create_cron_backup', [$backupSchedule])) {
            \WPStaging\functions\debug_log('[Schedule Backup Cron] Early bailed when registering the cron to create a backup on a schedule, because it already exists');

            return;
        }

        $this->registerScheduleInDb($backupSchedule);
        $this->reCreateCron();
    }

    /**
     * Registers a schedule in the Db.
     */
    protected function registerScheduleInDb($backupSchedule)
    {
        $backupSchedules = get_option(static::OPTION_BACKUP_SCHEDULES, []);
        if (!is_array($backupSchedules)) {
            $backupSchedules = [];
        }

        $backupSchedules[] = $backupSchedule;

        if (!update_option(static::OPTION_BACKUP_SCHEDULES, $backupSchedules, false)) {
            \WPStaging\functions\debug_log('[Schedule Backup Cron] Could not update BackupSchedules DB option');
        }
    }

    /**
     * AJAX callback that processes the backup schedule.
     *
     * @param $backupData
     */
    public function createCronBackup($backupData)
    {
        // Cron is hell to debug, so let's log everything that happens.
        $logId = wp_generate_password(4, false);

        \WPStaging\functions\debug_log("[Schedule Backup Cron - $logId] Received request to create a backup using Cron. Backup Data: " . wp_json_encode($backupData));

        try {
            \WPStaging\functions\debug_log("[Schedule Backup Cron - $logId] Preparing job");
            $jobId = WPStaging::make(PrepareExport::class)->prepare($backupData);
            \WPStaging\functions\debug_log("[Schedule Backup Cron - $logId] Successfully received a Job ID: $jobId");

            if ($jobId instanceof \WP_Error) {
                \WPStaging\functions\debug_log("[Schedule Backup Cron - $logId] Failed to create backup: " . $jobId->get_error_message());
            }
        } catch (\Exception $e) {
            \WPStaging\functions\debug_log("[Schedule Backup Cron - $logId] Exception thrown while preparing the Backup: " . $e->getMessage());
        }
    }

    /**
     * Ajax callback to dismiss a schedule.
     */
    public function dismissSchedule()
    {
        if (!current_user_can((new Capabilities())->manageWPSTG())) {
            return;
        }

        if (!(new Nonce())->requestHasValidNonce(Nonce::WPSTG_NONCE)) {
            return;
        }

        if (empty($_POST['scheduleId'])) {
            return;
        }

        try {
            $this->deleteSchedule(Sanitize::sanitizeString($_POST['scheduleId']));
            wp_send_json_success();
        } catch (\Exception $e) {
            wp_send_json_error($e->getMessage());
        }
    }

    /**
     * Deletes a backup schedule.
     *
     * @param string $scheduleId The schedule ID to delete.
     */
    public function deleteSchedule($scheduleId)
    {
        $schedules = $this->getSchedules();

        $newSchedules = array_filter($schedules, function ($schedule) use ($scheduleId) {
            return $schedule['scheduleId'] != $scheduleId;
        });

        if (!update_option(static::OPTION_BACKUP_SCHEDULES, $newSchedules, false)) {
            \WPStaging\functions\debug_log('[Schedule Backup Cron] Could not update BackupSchedules DB option after removing schedule.');
            throw new \RuntimeException('Could not unschedule event from Db.');
        }

        $this->reCreateCron();
    }

    /**
     * @see OPTION_BACKUP_SCHEDULES The Db option that is the source of truth for Cron events.
     *                              The backup schedule cron events are deleted and re-created
     *                              based on what is in this db option.
     *
     *                              This way, we only care about preserving this option on Backup
     *                              Restore or Push, and we don't have to worry about re-scheduling
     *                              the Cron events or removing leftover schedules.
     */
    public function reCreateCron()
    {
        $schedules = $this->getSchedules();
        static::removeBackupSchedulesFromCron();

        foreach ($schedules as $schedule) {
            $timeToSchedule = new \DateTime('now', wp_timezone());

            /**
             * New mechanism for recroning old jobs
             */
            if (isset(wp_get_schedules()[$schedule['schedule']]) && isset($schedule['firstSchedule'])) {
                $this->setNextSchedulingDate($timeToSchedule, $schedule);
            } else {
                $this->setUpcomingDateTime($timeToSchedule, $schedule['time']);
            }

            /** @see \WPStaging\Pro\Backup\BackupServiceProvider::enqueueAjaxListeners */
            $result = wp_schedule_event($timeToSchedule->format('U'), $schedule['schedule'], 'wpstg_create_cron_backup', [$schedule]);

            // Early bail: Could not register Cron event.
            if (!$result || $result instanceof \WP_Error) {
                if ($result instanceof \WP_Error) {
                    $details = $result->get_error_message();
                } else {
                    $details = '';
                }

                \WPStaging\functions\debug_log('[Schedule Backup Cron] Failed to register the cron event. ' . $details);

                return;
            }
        }
    }

    /**
     * Removes all backup schedule events from WordPress Cron array.
     *
     * This is static so that it can be called from WP STAGING deactivation hook
     * without having to bootstrap the entire plugin.
     *
     * This is a low-level function that can run when WP STAGING has not been
     * bootstrapped, so there's no autoload nor Container available.
     */
    public static function removeBackupSchedulesFromCron()
    {
        $cron = get_option('cron');

        // Bail: Unexpected value - should never happen.
        if (!is_array($cron)) {
            return false;
        }

        // Remove any backup schedules from Cron
        foreach ($cron as $timestamp => &$events) {
            if (is_array($events)) {
                foreach ($events as $callback => &$args) {
                    if ($callback === 'wpstg_create_cron_backup') {
                        unset($cron[$timestamp][$callback]);
                    }
                }
            }
        }

        // After removing the backup schedule events,
        // we might have timestamps with no events.
        // So we remove any leftover timestamps that don't have any events.
        $cron = array_filter($cron, function ($timestamps) {
            return !empty($timestamps);
        });

        update_option('cron', $cron);

        return true;
    }

    /**
     * Check cron status whether it is working or not
     * Logic is adopted from WP Crontrol plugin
     *
     * @return bool
     */
    public function checkCronStatus()
    {
        global $wp_version;

        $this->cronMessage = '';

        // Third party plugins that handle crons
        $thirdPartyCronPlugins = [
            '\HM\Cavalcade\Plugin\Job'         => 'Cavalcade',
            '\Automattic\WP\Cron_Control\Main' => 'Cron Control',
            '\KMM\KRoN\Core'                   => 'KMM KRoN',
        ];

        foreach ($thirdPartyCronPlugins as $class => $plugin) {
            if (class_exists($class)) {
                $this->cronMessage = sprintf(
                    __('WP Cron is being managed by the %s plugin.', 'wp-staging'),
                    $plugin
                );

                return true;
            }
        }

        if (defined('DISABLE_WP_CRON') && DISABLE_WP_CRON) {
            $this->cronMessage = sprintf(
                __('The backup background creation depends on WP CRON but %s is set to %s in wp-config.php. So backup background processing will not work. You can remove the constant %s or set its value to %s to make background processing work.', 'wp-staging'),
                '<code>DISABLE_WP_CRON</code>',
                '<code>true</code>',
                '<code>DISABLE_WP_CRON</code>',
                '<code>false</code>'
            );

            return true;
        }

        if (defined('ALTERNATE_WP_CRON') && ALTERNATE_WP_CRON) {
            $this->cronMessage = sprintf(
                __('The %s constant is set to true.', 'wp-staging'),
                'ALTERNATE_WP_CRON'
            );

            return true;
        }

        // Don't do the next time expensive checking if no schedules are set
        if ($this->isSchedulesEmpty()) {
            return true;
        }

        $sslverify   = version_compare($wp_version, '4.0', '<');
        $doingWpCron = sprintf('%.22F', microtime(true));

        $cronRequest = apply_filters('cron_request', [
            'url'  => add_query_arg('doing_wp_cron', $doingWpCron, site_url('wp-cron.php')),
            'key'  => $doingWpCron,
            'args' => [
                'timeout'   => 10,
                'blocking'  => true,
                'sslverify' => apply_filters('https_local_ssl_verify', $sslverify),
            ],
        ]);

        $cronRequest['args']['blocking'] = true;

        $result = wp_remote_post($cronRequest['url'], $cronRequest['args']);

        if (is_wp_error($result)) {
            $this->cronMessage = "Cron jobs do not work on this site. Can not create scheduled backups. Error message: " . $result->get_error_message();
            // Only send the error report mail if error is caused by WP STAGING
            if ($this->isWpstgError()) {
                $this->sendErrorReport($this->cronMessage);
            }

            return false;
        }

        if (wp_remote_retrieve_response_code($result) >= 300) {
            $this->cronMessage = sprintf(
                __('Unexpected HTTP response code: %s', 'wp-staging'),
                intval(wp_remote_retrieve_response_code($result))
            );

            return false;
        }

        return true;
    }

    /** @return string */
    public function getCronMessage()
    {
        return $this->cronMessage;
    }

    /**
     * @return array An array where the first item is the timestamp, and the second is the backup callback.
     * @throws \Exception When there is no backup scheduled or one could not be found.
     */
    public function getNextBackupSchedule()
    {
        $cron = get_option('cron');

        // Bail: Unexpected value - should never happen.
        if (!is_array($cron)) {
            throw new \UnexpectedValueException();
        }

        ksort($cron, SORT_NUMERIC);

        // Remove any backup schedules from Cron
        foreach ($cron as $timestamp => &$events) {
            if (is_array($events)) {
                foreach ($events as $callback => &$args) {
                    if ($callback === 'wpstg_create_cron_backup') {
                        return [$timestamp, $cron[$timestamp][$callback]];
                    }
                }
            }
        }

        // No results found
        throw new \OutOfBoundsException();
    }

    /**
     * Set date today or tommorrow for given DateTime object according to time
     *
     * @param \DateTime $datetime
     * @param string|array $time
     */
    protected function setUpcomingDateTime(&$datetime, $time)
    {
        if (is_array($time)) {
            $hourAndMinute = $time;
        } else {
            $hourAndMinute = explode(':', $time);
        }

        // The event should be scheduled later today or tomorrow? Compares "Hi (Hourminute)" timestamps to figure out.
        if ((int)sprintf('%s%s', $hourAndMinute[0], $hourAndMinute[1]) < (int)$datetime->format('Hi')) {
            $datetime->add(new \DateInterval('P1D'));
        }

        $datetime->setTime($hourAndMinute[0], $hourAndMinute[1]);
    }

    /**
     * Set the next scheduling date for the schedule
     *
     * @param \DateTime $datetime
     * @param array $schedule
     */
    protected function setNextSchedulingDate(&$datetime, $schedule)
    {
        $next = $schedule['firstSchedule'];
        $now = $datetime->getTimestamp();
        if ($next >= $now) {
            $this->setUpcomingDateTime($datetime, $schedule['time']);
            return;
        }

        $recurrance = wp_get_schedules()[$schedule['schedule']];
        while ($next < $now) {
            $next += $recurrance['interval'];
        }

        $datetime->setTimestamp($next);
    }

    /**
     * Detect whether the last error is caused by WP STAGING
     *
     * @return bool
     */
    protected function isWpstgError()
    {
        $error = error_get_last();
        if (!is_array($error)) {
            return false;
        }

        return strpos($error['file'], WPSTG_PLUGIN_SLUG) !== false;
    }

    /**
     * @param string $message
     */
    public function sendErrorReport($message)
    {
        if (get_option(self::BACKUP_SCHEDULE_ERROR_REPORT_OPTION) !== 'true') {
            return;
        }

        $reportEmail = get_option(self::BACKUP_SCHEDULE_REPORT_EMAIL_OPTION);
        if (!filter_var($reportEmail, FILTER_VALIDATE_EMAIL)) {
            return;
        }

        wp_mail($reportEmail, __('WP STAGING - Backup Schedules Error Report', 'wp-staging'), $message, [], []);
    }

    /**
     * @return bool
     */
    private function isSchedulesEmpty()
    {
        $schedules = get_option(static::OPTION_BACKUP_SCHEDULES, []);
        if (empty($schedules)) {
            return true;
        }
        return false;
    }
}
