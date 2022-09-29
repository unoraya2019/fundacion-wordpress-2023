<?php

namespace WPStaging\Pro\Backup;

use WPStaging\Pro\Backup\Entity\BackupMetadata;
use WPStaging\Pro\Backup\Service\BackupsFinder;

class BackupDeleter
{
    protected $backupsFinder;
    protected $backupMetadata;

    protected $errors = [];

    public function __construct(BackupsFinder $backupsFinder, BackupMetadata $backupMetadata)
    {
        $this->backupsFinder  = $backupsFinder;
        $this->backupMetadata = $backupMetadata;
    }

    /** @return array */
    public function getErrors()
    {
        return $this->errors;
    }

    public function deleteAllAutomatedDbOnlyBackups()
    {
        $this->errors = [];
        foreach ($this->backupsFinder->findBackups() as $backup) {
            $metadata = $this->backupMetadata->hydrateByFilePath($backup->getRealPath());
            if (
                $metadata->getIsAutomatedBackup() &&
                $metadata->getIsExportingDatabase() &&
                !$metadata->getIsExportingMuPlugins() &&
                !$metadata->getIsExportingPlugins() &&
                !$metadata->getIsExportingThemes() &&
                !$metadata->getIsExportingUploads() &&
                !$metadata->getIsExportingOtherWpContentFiles()
            ) {
                $deleted = unlink($backup->getRealPath());
                if (!$deleted) {
                    $this->errors[] = sprintf(__('Unable to delete database-only automated backups: %s', 'wp-staging'), $backup->getFilename());
                }
            }
        }
    }
}
