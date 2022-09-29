<?php

namespace WPStaging\Pro\Backup\Task\Tasks\JobExport;

use Exception;
use WPStaging\Framework\Queue\FinishedQueueException;
use WPStaging\Framework\Queue\SeekableQueueInterface;
use WPStaging\Framework\Utils\Cache\Cache;
use WPStaging\Pro\Backup\Dto\StepsDto;
use WPStaging\Pro\Backup\Exceptions\DiskNotWritableException;
use WPStaging\Pro\Backup\Exceptions\StorageException;
use WPStaging\Pro\Backup\Storage\RemoteUploaderInterface;
use WPStaging\Pro\Backup\Task\ExportTask;
use WPStaging\Vendor\Psr\Log\LoggerInterface;

abstract class AbstractStorageTask extends ExportTask
{
    /** @var RemoteUploaderInterface */
    protected $remoteUploader;

    public function __construct(LoggerInterface $logger, Cache $cache, StepsDto $stepsDto, SeekableQueueInterface $taskQueue, RemoteUploaderInterface $remoteUploader)
    {
        parent::__construct($logger, $cache, $stepsDto, $taskQueue);

        $this->remoteUploader = $remoteUploader;
    }

    public function execute()
    {
        $exportFilePath = $this->jobDataDto->getBackupFilePath();
        if ($this->remoteUploader->getError() !== false) {
            $this->logger->warning($this->remoteUploader->getError());
            return $this->generateResponse(false);
        }

        $this->remoteUploader->setupUpload($this->logger, $this->stepsDto, $this->jobDataDto);

        $canUpload = $this->remoteUploader->setBackupFilePath($exportFilePath);
        if (!$canUpload) {
            $this->logger->warning($this->remoteUploader->getError());
            $this->remoteUploader->stopUpload();
            return $this->generateResponse(false);
        }

        $uploaded = 0;
        $fileSize = size_format(filesize($exportFilePath), 2);
        while (!$this->isThreshold() && !$this->stepsDto->isFinished()) {
            try {
                $uploaded = $this->remoteUploader->chunkUpload();
            } catch (FinishedQueueException $exception) {
                $this->stepsDto->finish();
                return $this->finishUpload($fileSize);
            } catch (StorageException $exception) {
                $this->logger->error($exception->getMessage());
                $this->remoteUploader->stopUpload();
                return $this->generateResponse(false);
            } catch (DiskNotWritableException $exception) {
                // Probably disk full. No-op, as this is handled elsewhere.
            } catch (Exception $exception) {
                // Last chunk maybe. No-op
            }
        }

        if ($this->stepsDto->isFinished()) {
            return $this->finishUpload($fileSize);
        }

        $uploaded = size_format($uploaded, 2);
        $this->logger->info('Uploaded ' . $uploaded . '/' . $fileSize . ' of backup file');
        $this->remoteUploader->stopUpload();
        return $this->generateResponse(false);
    }

    private function finishUpload($fileSize)
    {
        $this->remoteUploader->stopUpload();
        $this->jobDataDto->setEndTime(time());
        $this->logger->info('Uploaded ' . $fileSize . '/' . $fileSize . ' of backup file');
        $this->logger->info('The backup upload finished.');
        return $this->generateResponse(false);
    }
}
