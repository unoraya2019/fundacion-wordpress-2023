<?php

namespace WPStaging\Pro\Backup\Ajax\FileList;

use WPStaging\Framework\Adapter\DateTimeAdapter;
use WPStaging\Framework\Adapter\Directory;
use WPStaging\Framework\Filesystem\Filesystem;
use WPStaging\Pro\Backup\Service\BackupsFinder;
use WPStaging\Pro\Backup\Entity\ListableBackup;
use WPStaging\Pro\Backup\Entity\BackupMetadata;
use WPStaging\Framework\Utils\Urls;
use SplFileInfo;

class ListableBackupsCollection
{
    private $directory;
    private $dateTimeAdapter;
    private $backupsFinder;
    private $filesystem;
    private $urls;


    public function __construct(DateTimeAdapter $dateTimeAdapter, BackupsFinder $backupsFinder, Directory $directory, Filesystem $filesystem, Urls $urls)
    {
        $this->dateTimeAdapter = $dateTimeAdapter;
        $this->directory       = $directory;
        $this->backupsFinder   = $backupsFinder;
        $this->filesystem      = $filesystem;
        $this->urls            = $urls;
    }

    /**
     * @return array<ListableBackup>
     */
    public function getListableBackups()
    {
        $backupFiles = $this->backupsFinder->findBackups();

        // Early bail: No backup files found.
        if (empty($backupFiles)) {
            return [];
        }

        $backups = [];

        /** @var SplFileInfo $file */
        foreach ($backupFiles as $file) {
            $md5Basename = md5($file->getBasename());

            /*
             * Prevent listing the same file twice if it's generated and also uploaded.
             * Uploaded files takes precedence as their iterator is appended first.
             */
            if (array_key_exists($md5Basename, $backups)) {
                continue;
            }

            $downloadUrl = $this->urls->getBackupUrl() . $file->getFilename();

            $relativePath = $file->getBasename();

            if ($file->getExtension() === 'wpstg') {
                try {
                    $backupMetadata = new BackupMetadata();
                    $backupMetadata = $backupMetadata->hydrateByFilePath($file->getRealPath());
                } catch (\Exception $e) {
                    $listableBackup = new ListableBackup();
                    $listableBackup->dateCreatedTimestamp = $file->getMTime();
                    $listableBackup->dateCreatedFormatted = $this->dateTimeAdapter->transformToWpFormat((new \DateTime())->setTimestamp($file->getMTime()));
                    $listableBackup->dateUploadedTimestamp = $file->getCTime();
                    $listableBackup->dateUploadedFormatted = $this->dateTimeAdapter->transformToWpFormat((new \DateTime())->setTimestamp($file->getCTime()));
                    $listableBackup->downloadUrl = $downloadUrl;
                    $listableBackup->relativePath = $relativePath;
                    $listableBackup->backupName = $relativePath;
                    $listableBackup->name = $file->getFilename();
                    $listableBackup->size = size_format($file->getSize(), 2);
                    $listableBackup->id = $md5Basename;
                    $listableBackup->md5BaseName = $md5Basename;
                    $listableBackup->corrupt = true;
                    $backups[$md5Basename] = $listableBackup;

                    continue;
                }

                $listableBackup                                 = new ListableBackup();
                $listableBackup->type                           = $backupMetadata->getSingleOrMulti();
                $listableBackup->subsiteType                    = $listableBackup->type === 'single' ? '' : ($backupMetadata->getSubdomainInstall() ? 'Subdomains' : 'Subdirectories');
                $listableBackup->automatedBackup                = $backupMetadata->getIsAutomatedBackup();
                $listableBackup->backupName                     = $backupMetadata->getName();
                $listableBackup->dateCreatedTimestamp           = $backupMetadata->getDateCreated();
                $listableBackup->dateCreatedFormatted           = $this->dateTimeAdapter->transformToWpFormat((new \DateTime())->setTimestamp($backupMetadata->getDateCreated()));
                $listableBackup->dateUploadedTimestamp          = $file->getCTime();
                $listableBackup->dateUploadedFormatted          = $this->dateTimeAdapter->transformToWpFormat((new \DateTime())->setTimestamp($file->getCTime()));
                $listableBackup->downloadUrl                    = $downloadUrl;
                $listableBackup->relativePath                   = $relativePath;
                $listableBackup->id                             = $backupMetadata->getDateCreated();
                $listableBackup->isExportingDatabase            = $backupMetadata->getIsExportingDatabase();
                $listableBackup->isExportingMuPlugins           = $backupMetadata->getIsExportingMuPlugins();
                $listableBackup->isExportingOtherWpContentFiles = $backupMetadata->getIsExportingOtherWpContentFiles();
                $listableBackup->isExportingPlugins             = $backupMetadata->getIsExportingPlugins();
                $listableBackup->isExportingThemes              = $backupMetadata->getIsExportingThemes();
                $listableBackup->isExportingUploads             = $backupMetadata->getIsExportingUploads();
                $listableBackup->generatedOnWPStagingVersion    = $backupMetadata->getVersion();
                $listableBackup->name                           = $file->getFilename();
                $listableBackup->notes                          = $backupMetadata->getNote();
                $listableBackup->size                           = size_format($file->getSize(), 2);
                $listableBackup->md5BaseName                    = $md5Basename;
            } elseif ($file->getExtension() === 'sql') {
                $listableBackup                      = new ListableBackup();
                $listableBackup->legacy              = true;
                $listableBackup->isExportingDatabase = true;
                $listableBackup->backupName          = $file->getBasename();
                $listableBackup->downloadUrl         = $downloadUrl;
                $listableBackup->name                = $file->getFilename();
                $listableBackup->size                = size_format($file->getSize(), 2);
                $listableBackup->md5BaseName         = $md5Basename;
            } else {
                continue;
            }

            $backups[$md5Basename] = $listableBackup;
        }

        return $backups;
    }
}
