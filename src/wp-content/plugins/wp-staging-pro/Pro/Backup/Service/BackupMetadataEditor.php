<?php

namespace WPStaging\Pro\Backup\Service;

use WPStaging\Framework\Filesystem\FileObject;
use WPStaging\Pro\Backup\Entity\BackupMetadata;

class BackupMetadataEditor
{
    /**
     * @param FileObject     $backupFile It must be opened with File::MODE_APPEND
     * @param BackupMetadata $newMetadata
     */
    public function setBackupMetadata(FileObject $backupFile, BackupMetadata $newMetadata)
    {
        $existingMetadataPosition = $backupFile->getExistingMetadataPosition();

        $backupFile->fseek($existingMetadataPosition);

        // Validate metadata position
        if (!is_array(json_decode($backupFile->readAndMoveNext(), true))) {
            throw new \UnexpectedValueException('Could not find the existing metadata from the backup.');
        }

        $backupFile->ftruncate($existingMetadataPosition);
        $backupFile->fseek($existingMetadataPosition);
        $backupFile->fwrite(json_encode($newMetadata) . PHP_EOL);
    }
}
