<?php

namespace WPStaging\Pro\Backup\Task\Tasks\JobExport;

use Exception;
use WPStaging\Framework\Filesystem\FileObject;
use WPStaging\Framework\Filesystem\PathIdentifier;
use WPStaging\Framework\Queue\SeekableQueueInterface;
use WPStaging\Framework\Utils\Cache\Cache;
use WPStaging\Pro\Backup\Dto\StepsDto;
use WPStaging\Pro\Backup\Entity\BackupMetadata;
use WPStaging\Pro\Backup\Service\BackupMetadataEditor;
use WPStaging\Pro\Backup\Task\ExportTask;
use WPStaging\Vendor\Psr\Log\LoggerInterface;

class ValidateBackupTask extends ExportTask
{
    /** @var PathIdentifier */
    protected $pathIdentifier;

    /** @var BackupMetadataEditor */
    protected $backupMetadataEditor;

    public function __construct(LoggerInterface $logger, Cache $cache, StepsDto $stepsDto, SeekableQueueInterface $taskQueue, PathIdentifier $pathIdentifier, BackupMetadataEditor $backupMetadataEditor)
    {
        parent::__construct($logger, $cache, $stepsDto, $taskQueue);

        $this->pathIdentifier = $pathIdentifier;
        $this->backupMetadataEditor = $backupMetadataEditor;
    }

    public static function getTaskName()
    {
        return 'backup_export_validate';
    }

    public static function getTaskTitle()
    {
        return 'Validating Backup Export';
    }

    public function execute()
    {
        $exportFilePath = $this->jobDataDto->getBackupFilePath();

        // Store the "Size" of the Backup in the metadata, which is something we can only do after the backup is final.
        try {
            $this->signBackup($exportFilePath);
        } catch (Exception $e) {
            $this->logger->critical('The backup file could not be signed for consistency.');

            return $this->generateResponse();
        }

        // Validate the Backup
        if ($exportFilePath) {
            try {
                $this->validateBackup($exportFilePath);
            } catch (Exception $e) {
                $this->logger->critical('The backup file seems to be invalid.');

                return $this->generateResponse();
            }
        }

        $this->stepsDto->finish();

        return $this->generateResponse(false);
    }

    /**
     * Signing the Backup aims to give it an identifier that can be checked for it's consistency.
     *
     * Currently, we use the size of the file. We can use this information later, during Restore or Upload,
     * to check if the Backup file we have is complete and matches the expected one.
     *
     * @param string $exportFilePath
     */
    protected function signBackup($exportFilePath)
    {
        clearstatcache();
        if (!is_file($exportFilePath)) {
            throw new \RuntimeException('The backup file is invalid.');
        }

        $file = new FileObject($exportFilePath, FileObject::MODE_APPEND_AND_READ);
        $backupMetadata = new BackupMetadata();
        $backupMetadata->hydrate($file->readBackupMetadata());

        /*
         * Before: "backupSize": ""
         * After:  "backupSize": 123456
         */
        $backupMetadata->setBackupSize($file->getSize() - 2 + strlen($file->getSize()));

        $this->backupMetadataEditor->setBackupMetadata($file, $backupMetadata);
    }

    /**
     * Check if the backup was successfully validated.
     *
     * @param string $exportFilePath
     */
    protected function validateBackup($exportFilePath)
    {
        clearstatcache();
        if (!is_file($exportFilePath)) {
            throw new \RuntimeException('The backup file is invalid.');
        }

        $file = new FileObject($exportFilePath);

        $backupMetadata = new BackupMetadata();
        $backupMetadata->hydrate($file->readBackupMetadata());

        if ($backupMetadata->getName() !== $this->jobDataDto->getName()) {
            throw new \RuntimeException('The backup file seems to be invalid (Unexpected Name in Metadata).');
        }

        if ($backupMetadata->getBackupSize() !== $file->getSize()) {
            throw new \RuntimeException('The backup file seems to be invalid (Unexpected Size in Metadata).');
        }

        $this->logger->info('The backup was validated successfully.');
    }
}
