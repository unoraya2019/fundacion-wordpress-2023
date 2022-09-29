<?php

// TODO PHP7.x; declare(strict_types=1);
// TODO PHP7.x; return types && type-hints

namespace WPStaging\Pro\Backup\Job;

use RuntimeException;
use WPStaging\Core\Utils\Logger;
use WPStaging\Framework\Adapter\Directory;
use WPStaging\Framework\Exceptions\WPStagingException;
use WPStaging\Framework\Filesystem\DiskWriteCheck;
use WPStaging\Framework\Filesystem\Filesystem;
use WPStaging\Framework\Interfaces\ShutdownableInterface;
use WPStaging\Framework\Traits\BenchmarkTrait;
use WPStaging\Pro\Backup\BackupProcessLock;
use WPStaging\Pro\Backup\Dto\AbstractDto;
use WPStaging\Pro\Backup\Exceptions\DiskNotWritableException;
use WPStaging\Pro\Backup\Exceptions\ProcessLockedException;
use WPStaging\Pro\Backup\Exceptions\TaskHealthException;
use WPStaging\Pro\Backup\Task\AbstractTask;
use WPStaging\Pro\Backup\Dto\TaskResponseDto;
use WPStaging\Framework\Queue\Queue;
use WPStaging\Framework\Queue\Storage\CacheStorage;
use WPStaging\Framework\Utils\Cache\Cache;
use WPStaging\Core\WPStaging;
use WPStaging\Pro\Backup\Dto\JobDataDto;

abstract class AbstractJob implements ShutdownableInterface
{
    use BenchmarkTrait;

    /** @var JobDataDto */
    protected $jobDataDto;

    /** @var Cache $jobDataCache Persists the JobDataDto in the filesystem. */
    private $jobDataCache;

    /** @var Queue Controls the queue of tasks. */
    private $jobQueue;

    /** @var string */
    protected $currentTaskName;

    /** @var AbstractTask */
    protected $currentTask;

    /** @var Filesystem */
    protected $filesystem;

    /** @var Directory */
    protected $directory;

    /** @var BackupProcessLock */
    protected $processLock;

    /** @var DiskWriteCheck */
    protected $diskFullCheck;

    public function __construct(
        Queue $jobQueue,
        CacheStorage $jobQueueStorage,
        Cache $jobDataCache,
        JobDataDto $jobDataDto,
        Filesystem $filesystem,
        Directory $directory,
        BackupProcessLock $processLock,
        DiskWriteCheck $diskFullCheck
    ) {
        $this->jobDataDto = $jobDataDto;
        $this->jobDataCache = $jobDataCache;
        $this->jobQueue = $jobQueue;
        $this->filesystem = $filesystem;
        $this->directory = $directory;

        $this->jobDataCache->setLifetime(HOUR_IN_SECONDS);
        $this->jobDataCache->setFilename('jobCache_' . $this::getJobName());
        #$this->jobDataCache->setPath(static::getJobCacheDirectory());

        #$this->jobQueueStorage->getCache()->setPath(static::getJobCacheDirectory());
        $this->jobQueue->setName($this->findCurrentJob());

        /** Persists the queue of tasks in the filesystem. */
        $this->jobQueue->setStorage($jobQueueStorage);

        $this->processLock = $processLock;
        $this->diskFullCheck = $diskFullCheck;
    }

    /**
     * Persists the Job status to the current cross-request caching system.
     *
     * This method will be invoked in the context of the WordPress `shutdown` hook and should
     * not be invoked out of that context if not with full knowledge of its side-effects.
     *
     * @return void The method has the side-effect of persisting the Job status to the caching
     *              system.
     */
    public function persist()
    {
        if ($this->jobDataDto->isStatusCheck()) {
            return;
        }

        try {
            $this->diskFullCheck->testDiskIsWriteable();
        } catch (DiskNotWritableException $e) {
            // no-op, this is handled on the beginning of the next request
        }

        if ($this->jobDataDto->isFinished() && !$this->jobDataDto->isCleaned()) {
            $this->cleanup();
            $this->jobDataDto->setCleaned();
            return;
        }

        if ($this->currentTask instanceof AbstractTask) {
            $this->jobDataDto->setQueueOffset($this->currentTask->getQueue()->getOffset());
            $this->currentTask->persistStepsDto();
        }

        $data = $this->jobDataDto->toArray();

        try {
            $this->jobDataCache->save($data, true);
        } catch (\Exception $e) {
            // no-op
        }
    }

    /**
     * This method will be called in the context of the WordPress `shutdown` action to
     * persist the Job status once and only once.
     *
     * @return void The method has the side-effect of persisting the Job status to the caching
     *              system.
     */
    public function onWpShutdown()
    {
        $this->persist();
    }

    /** @return string
     * @throws WPStagingException
     */
    public static function getJobName()
    {
        throw new WPStagingException('Any extending class MUST override the getJobName method.');
    }

    /** @return array */
    abstract protected function getJobTasks();

    /** @return TaskResponseDto */
    abstract protected function execute();

    /** @return void */
    abstract protected function init();

    /** @return TaskResponseDto */
    public function prepareAndExecute()
    {
        try {
            // Check if the last request bailed with a Disk Write failure flag.
            $this->diskFullCheck->hasDiskWriteTestFailed();
        } catch (DiskNotWritableException $e) {
            $response = new TaskResponseDto();
            $response->setStatus(false);
            $response->setStatus('JOB_FAIL');
            $response->addMessage([
                'type' => 'critical',
                'date' => $this->getFormattedDate(),
                'message' => $e->getMessage(),
            ]);

            $this->jobDataCache->delete();

            return $response;
        }

        try {
            try {
                $this->prepare();
            } catch (TaskHealthException $e) {
                $response = new TaskResponseDto();
                $response->setStatus(false);

                if ($e->getCode() === TaskHealthException::CODE_TASK_FAILED_TOO_MANY_TIMES) {
                    // Signal to JavaScript that this Job failed if no further requests should be made.
                    $response->setStatus('JOB_FAIL');
                    $response->addMessage([
                        'type' => 'critical',
                        'date' => $this->getFormattedDate(),
                        'message' => $e->getMessage(),
                    ]);

                    $this->jobDataCache->delete();
                } else {
                    $response->setStatus('JOB_RETRY');
                    $response->addMessage([
                        'type' => 'warning',
                        'date' => $this->getFormattedDate(),
                        'message' => $e->getMessage(),
                    ]);
                }

                return $response;
            } catch (RuntimeException $ex) {
                $response = new TaskResponseDto();
                $response->setStatus(false);

                $response->setStatus('JOB_FAIL');
                $response->addMessage([
                    'type' => 'critical',
                    'date' => $this->getFormattedDate(),
                    'message' => $ex->getMessage(),
                ]);

                $this->jobDataCache->delete();

                return $response;
            }

            $this->processLock->lockProcess();

            /** @var TaskResponseDto $response */
            $response = $this->execute();

            $this->processLock->unlockProcess();

            /*
             * Let's display the name of the task running now, instead
             * of the task that just run to the user.
             *
             * Since we already popped from the queue to get here,
             * the current item now is the next.
             */
            $nextTask = $this->jobQueue->current();

            if (is_subclass_of($nextTask, AbstractTask::class)) {
                $response->setStatusTitle(call_user_func("$nextTask::getTaskTitle"));
            }

            return $response;
        } catch (DiskNotWritableException $e) {
            /**
             * Assume a DiskWriteCheck flag has been set, so the next request can pick it up.
             *
             * @see DiskWriteCheck::testDiskIsWriteable()
             * @see DiskWriteCheck::hasDiskWriteTestFailed()
             */
            $response = new TaskResponseDto();
            $response->setStatus(false);
            $response->setStatus('JOB_RETRY');
            $response->addMessage([
                'type' => 'warning',
                'date' => $this->getFormattedDate(),
                'message' => $e->getMessage(),
            ]);

            return $response;
        }
    }

    /**
     * Save the queue job manually
     * Useful where wp shutdown hook is not called, for instance in WP-CLI
     *
     * @return void
     */
    public function saveQueueJob()
    {
        $this->jobQueue->save();
    }

    /**
     * @return JobDataDto
     */
    public function getJobDataDto()
    {
        return $this->jobDataDto;
    }

    /**
     * @var $jobDataDto JobDataDto
     */
    public function setJobDataDto($jobDataDto)
    {
        $this->jobDataDto = $jobDataDto;
    }

    /**
     * return @void
     */
    protected function checkLastTaskHealth()
    {
        // Early bail: No task health on a task that is retrying a failed request. We will evaluate that on the next request.
        if ($this->jobDataDto->getTaskHealthIsRetrying()) {
            $this->processLock->unlockProcess();
            $this->jobDataDto->setTaskHealthIsRetrying(false);

            return;
        }

        if (!$this->jobDataDto->getTaskHealthResponded()) {
            // This happens when the previous task started but never generated a response.
            $this->jobDataDto->setTaskHealthSequentialFailedRetries($this->jobDataDto->getTaskHealthSequentialFailedRetries() + 1);
            $this->jobDataCache->save($this->jobDataDto);

            if ($this->jobDataDto->getTaskHealthSequentialFailedRetries() >= 10) {
                throw TaskHealthException::taskFailedTooManyTimes();
            } else {
                // Re-enqueue the task to be processed in the next.
                $this->jobQueue->prepend($this->jobDataDto->getTaskHealthName());
                $this->jobDataDto->setTaskHealthIsRetrying(true);
                throw TaskHealthException::retryingTask($this->jobDataDto->getTaskHealthSequentialFailedRetries(), 10);
            }
        }
    }

    public function prepare()
    {
        $data = $this->jobDataCache->get([]);

        if ($data) {
            $this->jobDataDto->hydrate($data);
        }

        // From now on, classes that require a JobDataDto will receive this instance.
        WPStaging::getInstance()->getContainer()->singleton(JobDataDto::class, $this->jobDataDto);

        // TODO RPoC Hack
        $this->jobDataDto->setStatusCheck(!empty($_GET['action']) && $_GET['action'] === 'wpstg--backups--status');

        if ($this->jobDataDto->isStatusCheck()) {
            return;
        }

        if ($this->jobDataDto->isInit()) {
            $this->cleanup();
            $this->init();
            $this->jobQueue->reset();
            $this->addTasks($this->getJobTasks());
        } else {
            $this->checkLastTaskHealth();
        }

        try {
            $this->processLock->checkProcessLocked();
        } catch (ProcessLockedException $e) {
            wp_send_json_error($e->getMessage(), $e->getCode());
        }

        $this->jobDataDto->setInit(false);

        $this->currentTaskName = $this->jobQueue->pop();

        /** @var AbstractTask currentTask */
        $this->currentTask = WPStaging::getInstance()->get($this->currentTaskName);

        if (!$this->currentTask instanceof AbstractTask) {
            throw new \RuntimeException('Next task of queue job is null or invalid. Maybe the queue is not working correctly? currentTaskName: ' . $this->currentTaskName);
        }

        if (!$this->jobDataDto instanceof AbstractDto) {
            throw new \RuntimeException('Job Queue DTO is null or invalid.');
        }

        $this->currentTask->setJobContext($this);
        $this->currentTask->setJobDataDto($this->jobDataDto);
        $this->currentTask->setJobId($this->jobDataDto->getId());
        $this->currentTask->setJobName($this::getJobName());
        $this->currentTask->setDebug(defined('WPSTG_DEBUG') && WPSTG_DEBUG);

        // Initialize Task Health Status
        $this->jobDataDto->setTaskHealthName($this->currentTaskName);
        $this->jobDataDto->setTaskHealthResponded(false);
    }

    /** @return AbstractTask */
    public function getCurrentTask()
    {
        return $this->currentTask;
    }

    protected function cleanup()
    {
        $this->filesystem->delete($this->directory->getCacheDirectory());
        $this->filesystem->mkdir($this->directory->getCacheDirectory(), true);
    }

    /**
     * @param TaskResponseDto $response
     *
     * @return TaskResponseDto
     */
    protected function getResponse(TaskResponseDto $response)
    {
        $this->jobDataDto->setTaskHealthResponded(true);
        $this->jobDataDto->setTaskHealthSequentialFailedRetries(0);

        $response->setJob(substr($this->findCurrentJob(), 3));

        // Task is not done yet, add it to beginning of the queue again
        if (!$response->isStatus()) {
            $className = get_class($this->currentTask);
            $this->jobQueue->prepend($className);
        }

        if ($response->isStatus() && $this->jobQueue->count() === 0) {
            $this->jobDataDto->setFinished(true);

            return $response;
        }

        $response->setStatus(false);

        return $response;
    }

    private function findCurrentJob()
    {
        $class = explode('\\', static::class);

        return end($class);
    }

    protected function addTasks(array $tasks = [])
    {
        foreach ($tasks as $task) {
            $this->jobQueue->push($task);
        }
    }

    /**
     * @return string Formatted date string
     */
    private function getFormattedDate()
    {
        return current_time(Logger::LOG_DATETIME_FORMAT);
    }
}
