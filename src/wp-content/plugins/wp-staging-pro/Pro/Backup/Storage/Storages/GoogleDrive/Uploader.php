<?php

namespace WPStaging\Pro\Backup\Storage\Storages\GoogleDrive;

use Exception;
use WPStaging\Framework\Filesystem\FileObject;
use WPStaging\Framework\Queue\FinishedQueueException;
use WPStaging\Pro\Backup\Dto\Job\JobExportDataDto;
use WPStaging\Pro\Backup\Dto\StepsDto;
use WPStaging\Pro\Backup\Storage\RemoteUploaderInterface;
use WPStaging\Vendor\Google\Client as GoogleClient;
use WPStaging\Vendor\Google\Service\Drive as GoogleDriveService;
use WPStaging\Vendor\Google\Service\Drive\DriveFile as GoogleDriveFile;
use WPStaging\Vendor\Google\Http\MediaFileUpload as GoogleMediaFileUpload;
use WPStaging\Vendor\Psr\Log\LoggerInterface;

use function WPStaging\functions\debug_log;

class Uploader implements RemoteUploaderInterface
{
    /** @var GoogleClient */
    private $client;

    /** @var StepsDto */
    private $stepsDto;

    /** @var JobExportDataDto */
    private $jobDataDto;

    /** @var LoggerInterface */
    private $logger;

    /** @var string */
    private $filePath;

    /** @var string */
    private $folderId;

    /** @var string */
    private $folderName;

    /** @var int */
    private $maxBackupsToKeep;

    /** @var GoogleDriveService */
    private $service;

    /** @var FileObject */
    private $fileObject;

    /** @var GoogleMediaFileUpload */
    private $media;

    /** @var int */
    private $chunkSize;

    /** @var Auth */
    private $auth;

    /** @var bool|string */
    private $error;

    public function __construct(Auth $auth)
    {
        $this->error = false;
        $this->auth = $auth;

        if (!$this->auth->isGuzzleAvailable()) {
            $this->error = __('cURL extension is missing. Backup is still available locally.', 'wp-staging');
            return;
        }

        if (!$this->auth->isAuthenticated()) {
            $this->error = __('Google Drive is not authenticated. Backup is still available locally.', 'wp-staging');
            return;
        }

        $this->client = $auth->setClientWithAuthToken();
        $options = $this->auth->getOptions();
        $this->folderName = isset($options['folderName']) ? $options['folderName'] : Auth::FOLDER_NAME;
        $this->maxBackupsToKeep = isset($options['maxBackupsToKeep']) ? $options['maxBackupsToKeep'] : 0;
        $this->maxBackupsToKeep = intval($this->maxBackupsToKeep);
    }

    public function getProviderName()
    {
        return 'Google Drive';
    }

    public function setupUpload(LoggerInterface $logger, StepsDto $stepsDto, JobExportDataDto $jobDataDto, $chunkSize = 1 * 1024 * 1024)
    {
        $this->logger = $logger;
        $this->stepsDto = $stepsDto;
        $this->jobDataDto = $jobDataDto;
        $this->chunkSize = $chunkSize;
    }

    public function setBackupFilePath($backupFilePath)
    {
        $this->filePath = $backupFilePath;
        $this->fileObject = new FileObject($this->filePath, FileObject::MODE_READ);
        $this->service = new GoogleDriveService($this->client);

        if (!$this->checkDiskSpace()) {
            return false;
        }

        $this->folderId = $this->auth->getFolderIdByName($this->folderName, $this->service);

        if (!$this->stepsDto->getTotal()) {
            $this->deleteOldestFile();
        }

        $fileMetadata = new GoogleDriveFile([
            'name' => $this->fileObject->getBasename(),
            'parents' => [$this->folderId],
        ]);

        $this->client->setDefer(true);

        $request = $this->service->files->create($fileMetadata);
        $this->media = new GoogleMediaFileUpload(
            $this->client,
            $request,
            'application/octet-stream',
            null,
            true,
            $this->chunkSize
        );

        $this->media->setFileSize($this->fileObject->getSize());

        if (!$this->stepsDto->getTotal()) {
            $this->stepsDto->setTotal($this->fileObject->getSize());
            $this->stepsDto->setCurrent(0);
            $this->jobDataDto->setRemoteStorageMeta([
                'ResumeURI' => $this->media->getResumeUri()
            ]);
            $this->logger->info('Google Drive Initiate backup upload to ' . $this->getProviderName());
            return true;
        }

        $resumeURI = $this->jobDataDto->getRemoteStorageMeta()['ResumeURI'];
        $this->media->resume($resumeURI);
        $newResumeURI = $this->media->getResumeUri();
        if ($newResumeURI !== $resumeURI) {
            $this->jobDataDto->setRemoteStorageMeta([
                'ResumeURI' => $newResumeURI
            ]);
        }

        return true;
    }

    /**
     * @param string $filePath
     * @param StepsDto $stepsDto
     * @param int $chunkSize
     *
     * @return int
     */
    public function chunkUpload()
    {
        $status = false;
        $offset = $this->stepsDto->getCurrent();

        $this->fileObject->fseek($offset);
        $chunk = $this->fileObject->fread($this->chunkSize);
        $status = $this->media->nextChunk($chunk);

        $offset += strlen($chunk);

        if ($status !== false) {
            throw new FinishedQueueException();
        }

        $this->stepsDto->setCurrent($offset);

        return $offset;
    }

    public function stopUpload()
    {
        $this->client->setDefer(false);
    }

    public function getError()
    {
        return $this->error;
    }

    private function deleteOldestFile()
    {
        if ($this->maxBackupsToKeep === 0) {
            return true;
        }

        $filesStored = $this->service->files->listFiles([
            'q'       => "'" . $this->folderId . "' in parents",
            'fields'  => 'nextPageToken, files(id, name, mimeType)',
            'orderBy' => 'createdTime'
        ]);

        if (sizeof($filesStored->getFiles()) < $this->maxBackupsToKeep) {
            return true;
        }

        $fileToDelete = $filesStored->getFiles()[0];
        $this->service->files->delete($fileToDelete->getId());
        $this->logger->info('Google Drive - Deleted oldest backup file');
        return true;
    }

    /**
     * @param GoogleDriveService $service
     * @return bool
     */
    private function checkDiskSpace($service = null)
    {
        if (apply_filters('wpstg.googleDrive.bypassDiskSpace', false)) {
            return true;
        }

        if ($service === null) {
            $service = $this->service;
        }

        try {
            $storage = $this->auth->getStorageInfo($service);
            $totalQuota = $storage->getLimit();
            $usedQuota = $storage->getUsage();
        } catch (Exception $ex) {
            return true;
        }

        if (!is_numeric($totalQuota) || !is_numeric($usedQuota)) {
            $this->logger->warning('Unable to get size of used or available storage space. Continuing with Upload to Google Drive!');
            return true;
        }

        $availableQuota = $totalQuota - $usedQuota;
        if (empty($availableQuota) || !is_numeric($availableQuota) || $availableQuota < 0) {
            return true;
        }

        if ($this->fileObject->getSize() > $availableQuota) {
            $this->error = sprintf(__('Google Drive Disk Quota Exceeded. Backup Size: %s. Space Available: %s. Backup is still available locally.', 'wp-staging'), size_format($this->fileObject->getSize(), 2), size_format($availableQuota, 2));
            return false;
        }

        return true;
    }
}
