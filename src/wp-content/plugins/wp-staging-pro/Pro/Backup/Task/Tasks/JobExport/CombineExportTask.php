<?php

// TODO PHP7.x; declare(strict_type=1);
// TODO PHP7.x; type hints & return types
// TODO PHP7.1; constant visibility

namespace WPStaging\Pro\Backup\Task\Tasks\JobExport;

use Exception;
use WPStaging\Framework\Analytics\Actions\AnalyticsBackupCreate;
use WPStaging\Framework\Filesystem\PathIdentifier;
use WPStaging\Framework\Queue\SeekableQueueInterface;
use WPStaging\Framework\Utils\Cache\Cache;
use WPStaging\Pro\Backup\Dto\StepsDto;
use WPStaging\Pro\Backup\Dto\Task\Export\Response\CombineExportResponseDto;
use WPStaging\Pro\Backup\Entity\BackupMetadata;
use WPStaging\Pro\Backup\Service\BackupMetadataEditor;
use WPStaging\Pro\Backup\Task\ExportTask;
use WPStaging\Vendor\Psr\Log\LoggerInterface;
use WPStaging\Pro\Backup\Service\Compressor;

class CombineExportTask extends ExportTask
{
    /** @var Compressor */
    private $compressor;
    private $wpdb;

    /** @var PathIdentifier */
    protected $pathIdentifier;

    /** @var BackupMetadataEditor */
    protected $backupMetadataEditor;

    /** @var AnalyticsBackupCreate */
    protected $analyticsBackupCreate;

    public function __construct(Compressor $compressor, LoggerInterface $logger, Cache $cache, StepsDto $stepsDto, SeekableQueueInterface $taskQueue, PathIdentifier $pathIdentifier, BackupMetadataEditor $backupMetadataEditor, AnalyticsBackupCreate $analyticsBackupCreate)
    {
        parent::__construct($logger, $cache, $stepsDto, $taskQueue);

        global $wpdb;
        $this->compressor = $compressor;
        $this->wpdb = $wpdb;
        $this->pathIdentifier = $pathIdentifier;
        $this->backupMetadataEditor = $backupMetadataEditor;
        $this->analyticsBackupCreate = $analyticsBackupCreate;
    }

    public static function getTaskName()
    {
        return 'backup_export_combine';
    }

    public static function getTaskTitle()
    {
        return 'Preparing Backup File';
    }

    public function execute()
    {
        $compressorDto = $this->compressor->getDto();

        $backupMetadata = $compressorDto->getBackupMetadata();
        $backupMetadata->setTotalDirectories($this->jobDataDto->getTotalDirectories());
        $backupMetadata->setTotalFiles($this->jobDataDto->getTotalFiles());
        $backupMetadata->setName($this->jobDataDto->getName());
        $backupMetadata->setIsAutomatedBackup($this->jobDataDto->getIsAutomatedBackup());

        global $wpdb;
        $backupMetadata->setPrefix($wpdb->base_prefix);

        // What the backup exports
        $backupMetadata->setIsExportingPlugins($this->jobDataDto->getIsExportingPlugins());
        $backupMetadata->setIsExportingMuPlugins($this->jobDataDto->getIsExportingMuPlugins());
        $backupMetadata->setIsExportingThemes($this->jobDataDto->getIsExportingThemes());
        $backupMetadata->setIsExportingUploads($this->jobDataDto->getIsExportingUploads());
        $backupMetadata->setIsExportingOtherWpContentFiles($this->jobDataDto->getIsExportingOtherWpContentFiles());
        $backupMetadata->setIsExportingDatabase($this->jobDataDto->getIsExportingDatabase());
        $backupMetadata->setScheduleId($this->jobDataDto->getScheduleId());

        $this->addSystemInfoToBackupMetadata($backupMetadata);

        if ($this->jobDataDto->getIsExportingDatabase()) {
            $backupMetadata->setDatabaseFile($this->pathIdentifier->transformPathToIdentifiable($this->jobDataDto->getDatabaseFile()));
            $backupMetadata->setDatabaseFileSize($this->jobDataDto->getDatabaseFileSize());

            $maxTableLength = 0;
            foreach ($this->jobDataDto->getTablesToExport() as $table) {
                // Get the biggest table name, without the prefix.
                $maxTableLength = max($maxTableLength, strlen(substr($table, strlen($this->wpdb->base_prefix))));
            }

            $backupMetadata->setMaxTableLength($maxTableLength);
        }

        if ($this->jobDataDto->getIsExportingPlugins()) {
            $backupMetadata->setPlugins(array_keys(get_plugins()));
        }

        if ($this->jobDataDto->getIsExportingMuPlugins()) {
            $backupMetadata->setMuPlugins(array_keys(get_mu_plugins()));
        }

        if ($this->jobDataDto->getIsExportingThemes()) {
            $backupMetadata->setThemes(array_keys(search_theme_directories()));
        }

        if (is_multisite() && is_main_site()) {
            $backupMetadata->setSites($this->jobDataDto->getSitesToExport());
        }

        try {
            // Write the Backup metadata
            $backupFilePath = $this->compressor->generateBackupMetadata();

            $this->jobDataDto->setBackupFilePath($backupFilePath);
            $this->stepsDto->finish();
            $this->logger->info(esc_html__('Successfully created backup file', 'wp-staging'));

            return $this->generateResponse(false);
        } catch (Exception $e) {
            $this->logger->critical('Failed to create backup file: ' . $e->getMessage());
        }


        $steps = $this->stepsDto;
        $steps->setCurrent($compressorDto->getWrittenBytesTotal());
        $steps->setTotal($compressorDto->getFileSize());

        $this->logger->info(sprintf('Written %d bytes to compressed export', $compressorDto->getWrittenBytesTotal()));

        return $this->generateResponse(false);
    }

    /**
     * @see \wp_version_check
     * @see https://codex.wordpress.org/Converting_Database_Character_Sets
     */
    protected function addSystemInfoToBackupMetadata(BackupMetadata &$backupMetadata)
    {
        /**
         * @var string $wp_version
         * @var int    $wp_db_version
         */
        include ABSPATH . WPINC . '/version.php';

        $mysqlVersion = preg_replace('/[^0-9.].*/', '', $this->wpdb->db_version());

        $backupMetadata->setPhpVersion(phpversion());
        $backupMetadata->setWpVersion($wp_version);
        /** @phpstan-ignore-line */
        $backupMetadata->setWpDbVersion($wp_db_version);
        /** @phpstan-ignore-line */
        $backupMetadata->setDbCollate($this->wpdb->collate);
        $backupMetadata->setDbCharset($this->wpdb->charset);
        $backupMetadata->setSqlServerVersion($mysqlVersion);
    }

    protected function getResponseDto()
    {
        return new CombineExportResponseDto();
    }
}
