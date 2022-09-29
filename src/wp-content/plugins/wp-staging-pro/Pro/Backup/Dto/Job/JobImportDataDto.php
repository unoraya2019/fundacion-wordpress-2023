<?php

namespace WPStaging\Pro\Backup\Dto\Job;

use WPStaging\Pro\Backup\Dto\JobDataDto;
use WPStaging\Pro\Backup\Entity\BackupMetadata;

class JobImportDataDto extends JobDataDto
{
    /** @var string */
    private $file;

    /** @var BackupMetadata */
    private $backupMetadata;

    /** @var string */
    protected $tmpDirectory;

    /** @var int Number of extracted files */
    private $extractorFilesExtracted = 0;

    /** @var int Number of written bytes to process the current files */
    private $extractorFileWrittenBytes = 0;

    private $extractorMetadataIndexPosition = 0;

    /** @var string Database table prefix to use while importing the backup */
    private $tmpDatabasePrefix;

    /** @var string Table being inserted during import. */
    private $tableToImport;

    /** @var bool Whether a transaction is started. */
    private $transactionStarted;

    /** @var array Store short names tables to drop */
    private $shortNamesTablesToDrop = [];

    /** @var array Store short names tables to import */
    private $shortNamesTablesToImport = [];

    /** @var bool */
    private $requireShortNamesForTablesToDrop = false;

    /** @var bool */
    private $requireShortNamesForTablesToImport = false;

    /**
     * @return string The .wpstg backup file being imported.
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Called dynamically
     * @see \WPStaging\Pro\Backup\Ajax\Import\PrepareImport::setupInitialData
     *
     * @param string $file
     */
    public function setFile($file)
    {
        $this->file = untrailingslashit(wp_normalize_path($file));
    }

    /**
     * @return BackupMetadata|null
     */
    public function getBackupMetadata()
    {
        return $this->backupMetadata;
    }

    /**
     * @param $backupMetadata
     */
    public function setBackupMetadata($backupMetadata)
    {
        if ($backupMetadata instanceof BackupMetadata) {
            $this->backupMetadata = $backupMetadata;

            return;
        }
        if (is_array($backupMetadata)) {
            try {
                $this->backupMetadata = (new BackupMetadata())->hydrate($backupMetadata);

                return;
            } catch (\Exception $e) {
                $this->backupMetadata = null;

                return;
            }
        }

        $this->backupMetadata = null;
    }

    /**
     * @return string
     */
    public function getTmpDirectory()
    {
        return $this->tmpDirectory;
    }

    /**
     * @param string $tmpPath
     */
    public function setTmpDirectory($tmpPath)
    {
        $this->tmpDirectory = trailingslashit(wp_normalize_path($tmpPath));
    }

    /**
     * @return int
     */
    public function getExtractorFilesExtracted()
    {
        return (int)$this->extractorFilesExtracted;
    }

    public function setExtractorFilesExtracted($extractorFilesExtracted)
    {
        $this->extractorFilesExtracted = (int)$extractorFilesExtracted;
    }

    public function incrementExtractorFilesExtracted()
    {
        $this->extractorFilesExtracted++;
    }

    /**
     * @return int
     */
    public function getExtractorFileWrittenBytes()
    {
        return (int)$this->extractorFileWrittenBytes;
    }

    /**
     * @param int $fileWrittenBytes
     */
    public function setExtractorFileWrittenBytes($fileWrittenBytes)
    {
        $this->extractorFileWrittenBytes = (int)$fileWrittenBytes;
    }

    /**
     * @return int
     */
    public function getExtractorMetadataIndexPosition()
    {
        return (int)$this->extractorMetadataIndexPosition;
    }

    /**
     * @param int $extractorMetadataIndexPosition
     */
    public function setExtractorMetadataIndexPosition($extractorMetadataIndexPosition)
    {
        $this->extractorMetadataIndexPosition = (int)$extractorMetadataIndexPosition;
    }

    /**
     * @return string
     */
    public function getTmpDatabasePrefix()
    {
        return $this->tmpDatabasePrefix;
    }

    /**
     * @param string $tmpDatabasePrefix
     */
    public function setTmpDatabasePrefix($tmpDatabasePrefix)
    {
        $this->tmpDatabasePrefix = $tmpDatabasePrefix;
    }

    /**
     * @return string
     */
    public function getTableToImport()
    {
        return $this->tableToImport;
    }

    /**
     * @param string $tableToImport
     */
    public function setTableToImport($tableToImport)
    {
        $this->tableToImport = $tableToImport;
    }

    /**
     * @return bool
     */
    public function getTransactionStarted()
    {
        return $this->transactionStarted;
    }

    /**
     * @param bool $transactionStarted
     */
    public function setTransactionStarted($transactionStarted)
    {
        $this->transactionStarted = $transactionStarted;
    }

    /**
     * @return array
     */
    public function getShortNamesTablesToDrop()
    {
        return $this->shortNamesTablesToDrop;
    }

    /**
     * @param array $tables
     */
    public function setShortNamesTablesToDrop($tables = [])
    {
        $this->shortNamesTablesToDrop = $tables;
    }

    /**
     * @param string $originalName
     * @param string $shorterName
     */
    public function addShortNameTableToDrop($originalName, $shorterName)
    {
        $this->shortNamesTablesToDrop[$shorterName] = $originalName;
    }

    /**
     * @return array
     */
    public function getShortNamesTablesToImport()
    {
        return $this->shortNamesTablesToImport;
    }

    /**
     * @param array $tables
     */
    public function setShortNamesTablesToImport($tables = [])
    {
        $this->shortNamesTablesToImport = $tables;
    }

    /**
     * @param string $originalName
     * @param string $shorterName
     */
    public function addShortNameTableToImport($originalName, $shorterName)
    {
        $this->shortNamesTablesToImport[$shorterName] = $originalName;
    }

    /**
     * @return bool
     */
    public function getRequireShortNamesForTablesToImport()
    {
        return $this->requireShortNamesForTablesToImport;
    }

    /**
     * @param bool $require
     */
    public function setRequireShortNamesForTablesToImport($require = false)
    {
        $this->requireShortNamesForTablesToImport = $require;
    }

    /**
     * @return bool
     */
    public function getRequireShortNamesForTablesToDrop()
    {
        return $this->requireShortNamesForTablesToDrop;
    }

    /**
     * @param bool $require
     */
    public function setRequireShortNamesForTablesToDrop($require = false)
    {
        $this->requireShortNamesForTablesToDrop = $require;
    }
}
