<?php

namespace WPStaging\Pro\Backup\Dto\Job;

use WPStaging\Pro\Backup\Dto\JobDataDto;
use WPStaging\Pro\Backup\Dto\Traits\IsExportingTrait;

class JobExportDataDto extends JobDataDto
{
    use IsExportingTrait;

    /** @var string|null */
    private $name;

    /** @var array */
    private $excludedDirectories = [];

    /** @var bool */
    private $isAutomatedBackup = false;

    /** @var int */
    private $totalDirectories;

    /** @var int The number of files in the backup index */
    private $totalFiles;

    /** @var int The number of files the FilesystemScanner discovered */
    private $discoveredFiles;

    /** @var string */
    private $databaseFile;

    /**
     * @var int If a file couldn't be processed in a single request,
     *          this property holds how many bytes were written thus far
     *          so that the export can start writing from this byte onwards.
     */
    private $fileBeingExportedWrittenBytes;

    /** @var int */
    private $totalRowsExported;

    /** @var int */
    private $tableRowsOffset = 0;

    /** @var int */
    private $totalRowsOfTableBeingExported = 0;

    /** @var array */
    private $tablesToExport = [];

    /** @var int The size in bytes of the database in this backup */
    private $databaseFileSize = 0;

    /** @var int The size in bytes of the filesystem in this backup */
    private $filesystemSize = 0;

    /** @var int The number of requests that the Discovering Files task has executed so far */
    private $discoveringFilesRequests = 0;

    /** @var bool True if this backup should be repeated on a schedule, false if it should run only once. */
    private $repeatBackupOnSchedule;

    /** @var string The cron to repeat this backup, if scheduled. */
    private $scheduleRecurrence;

    /** @var array The hour and minute to repeat this backup, if scheduled. */
    private $scheduleTime;

    /** @var int How many backups to keep, if scheduled. */
    private $scheduleRotation;

    /** @var string The absolute path to this .wpstg file */
    private $backupFilePath;

    /** @var string If set, this backup was created as part of this schedule ID. */
    private $scheduleId;

    /** @var array Selected storages for backup. */
    private $storages;

    /**
     * @var array The meta data used by used by Remote Storages to help uploading.
     * Stores ResumeURI for Google Drive
     * Stores UploadId and UploadedParts Meta for Amazon S3
     */
    private $remoteStorageMeta;

    /** @var bool Should this scheduled backup be created right now. Matters only if this backup is repeated on schedule */
    private $isCreateScheduleBackupNow;

    /** @var array Site selected to export */
    private $sitesToExport = [];

    /**
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Hydrated dynamically.
     *
     * @param string|null $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return array|null
     */
    public function getExcludedDirectories()
    {
        return (array)$this->excludedDirectories;
    }

    public function setExcludedDirectories(array $excludedDirectories = [])
    {
        $this->excludedDirectories = $excludedDirectories;
    }

    /**
     * @return bool
     */
    public function getIsAutomatedBackup()
    {
        return (bool)$this->isAutomatedBackup;
    }

    /**
     * Hydrated dynamically.
     *
     * @param bool $isAutomatedBackup
     */
    public function setIsAutomatedBackup($isAutomatedBackup)
    {
        $this->isAutomatedBackup = $isAutomatedBackup;
    }

    /**
     * @return int
     */
    public function getTotalDirectories()
    {
        return $this->totalDirectories;
    }

    /**
     * @param int $totalDirectories
     */
    public function setTotalDirectories($totalDirectories)
    {
        $this->totalDirectories = $totalDirectories;
    }

    /**
     * @return int
     */
    public function getTotalFiles()
    {
        return $this->totalFiles;
    }

    /**
     * @param int $totalFiles
     */
    public function setTotalFiles($totalFiles)
    {
        $this->totalFiles = $totalFiles;
    }

    /**
     * @return int
     */
    public function getDiscoveredFiles()
    {
        return $this->discoveredFiles;
    }

    /**
     * @param int $discoveredFiles
     */
    public function setDiscoveredFiles($discoveredFiles)
    {
        $this->discoveredFiles = $discoveredFiles;
    }

    /**
     * @return string
     */
    public function getDatabaseFile()
    {
        return $this->databaseFile;
    }

    /**
     * @param string $databaseFile
     */
    public function setDatabaseFile($databaseFile)
    {
        $this->databaseFile = $databaseFile;
    }

    /**
     * @return int
     */
    public function getTableRowsOffset()
    {
        return (int)$this->tableRowsOffset;
    }

    /**
     * @param int $tableRowsOffset
     */
    public function setTableRowsOffset($tableRowsOffset)
    {
        $this->tableRowsOffset = (int)$tableRowsOffset;
    }

    /**
     * @return int
     */
    public function getTotalRowsExported()
    {
        return (int)$this->totalRowsExported;
    }

    /**
     * @param int $totalRowsExported
     */
    public function setTotalRowsExported($totalRowsExported)
    {
        $this->totalRowsExported = (int)$totalRowsExported;
    }

    /**
     * @return int
     */
    public function getFileBeingExportedWrittenBytes()
    {
        return (int)$this->fileBeingExportedWrittenBytes;
    }

    /**
     * @param int $fileBeingExportedWrittenBytes
     */
    public function setFileBeingExportedWrittenBytes($fileBeingExportedWrittenBytes)
    {
        $this->fileBeingExportedWrittenBytes = (int)$fileBeingExportedWrittenBytes;
    }

    /**
     * @return array
     */
    public function getTablesToExport()
    {
        return (array)$this->tablesToExport;
    }

    /**
     * @param array $tablesToExport
     */
    public function setTablesToExport($tablesToExport)
    {
        $this->tablesToExport = (array)$tablesToExport;
    }

    /**
     * @return int
     */
    public function getTotalRowsOfTableBeingExported()
    {
        return (int)$this->totalRowsOfTableBeingExported;
    }

    /**
     * @param int $totalRowsOfTableBeingExported
     */
    public function setTotalRowsOfTableBeingExported($totalRowsOfTableBeingExported)
    {
        $this->totalRowsOfTableBeingExported = (int)$totalRowsOfTableBeingExported;
    }

    /**
     * @return int
     */
    public function getDatabaseFileSize()
    {
        return $this->databaseFileSize;
    }

    /**
     * @param int $databaseFileSize
     */
    public function setDatabaseFileSize($databaseFileSize)
    {
        $this->databaseFileSize = $databaseFileSize;
    }

    /**
     * @return int
     */
    public function getFilesystemSize()
    {
        return $this->filesystemSize;
    }

    /**
     * @param int $filesystemSize
     */
    public function setFilesystemSize($filesystemSize)
    {
        $this->filesystemSize = $filesystemSize;
    }

    /**
     * @return int
     */
    public function getDiscoveringFilesRequests()
    {
        return $this->discoveringFilesRequests;
    }

    /**
     * @param int $discoveringFilesRequests
     */
    public function setDiscoveringFilesRequests($discoveringFilesRequests)
    {
        $this->discoveringFilesRequests = $discoveringFilesRequests;
    }

    /**
     * @return bool
     */
    public function getRepeatBackupOnSchedule()
    {
        return $this->repeatBackupOnSchedule;
    }

    /**
     * @param bool $repeatBackupOnSchedule
     */
    public function setRepeatBackupOnSchedule($repeatBackupOnSchedule)
    {
        $this->repeatBackupOnSchedule = $repeatBackupOnSchedule;
    }

    /**
     * @see Cron For WP STAGING cron recurrences.
     *
     * @return string A WP STAGING cron schedule
     */
    public function getScheduleRecurrence()
    {
        return $this->scheduleRecurrence;
    }

    /**
     * @param string $scheduleRecurrence
     */
    public function setScheduleRecurrence($scheduleRecurrence)
    {
        $this->scheduleRecurrence = $scheduleRecurrence;
    }

    /**
     * @return array H:i time format, expected to be accurate to the site's timezone, example: 00:00
     */
    public function getScheduleTime()
    {
        return $this->scheduleTime;
    }

    /**
     * @param array $scheduleTime Hour and Minute ['00', '00']
     */
    public function setScheduleTime(array $scheduleTime)
    {
        $this->scheduleTime = $scheduleTime;
    }

    /**
     * @return int How many backups to keep, example: 1
     */
    public function getScheduleRotation()
    {
        return $this->scheduleRotation;
    }

    /**
     * @param int $scheduleRotation
     */
    public function setScheduleRotation($scheduleRotation)
    {
        $this->scheduleRotation = $scheduleRotation;
    }

    /**
     * @return string
     */
    public function getBackupFilePath()
    {
        return $this->backupFilePath;
    }

    /**
     * @param string $backupFilePath
     */
    public function setBackupFilePath($backupFilePath)
    {
        $this->backupFilePath = $backupFilePath;
    }

    /**
     * @return string
     */
    public function getScheduleId()
    {
        return $this->scheduleId;
    }

    /**
     * @param string $scheduleId
     */
    public function setScheduleId($scheduleId)
    {
        $this->scheduleId = $scheduleId;
    }

    /**
     * @return array
     */
    public function getStorages()
    {
        return $this->storages;
    }

    /**
     * @param string|array $storages
     */
    public function setStorages($storages)
    {
        if (!is_array($storages)) {
            $storages = json_decode($storages, true);
        }

        $this->storages = $storages;
    }

    /**
     * @return array
     */
    public function getRemoteStorageMeta()
    {
        return $this->remoteStorageMeta;
    }

    /**
     * @param array $remoteStorageMeta
     */
    public function setRemoteStorageMeta($remoteStorageMeta)
    {
        $this->remoteStorageMeta = $remoteStorageMeta;
    }

    /**
     * @return bool
     */
    public function getIsCreateScheduleBackupNow()
    {
        return $this->isCreateScheduleBackupNow;
    }

    /**
     * @param string $isCreateScheduleBackupNow
     */
    public function setIsCreateScheduleBackupNow($isCreateScheduleBackupNow)
    {
        $this->isCreateScheduleBackupNow = $isCreateScheduleBackupNow;
    }

    /**
     * @return array|null
     */
    public function getSitesToExport()
    {
        return (array)$this->sitesToExport;
    }

    public function setSitesToExport(array $sitesToExport = [])
    {
        $this->sitesToExport = $sitesToExport;
    }

    /** @return bool */
    public function isUploadToGoogleDrive()
    {
        return in_array('googleDrive', $this->getStorages());
    }

    /** @return bool */
    public function isUploadToAmazonS3()
    {
        return in_array('amazonS3', $this->getStorages());
    }

    /** @return bool */
    public function isUploadToSftp()
    {
        return in_array('sftp', $this->getStorages());
    }
}
