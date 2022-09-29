<?php

namespace WPStaging\Pro\Backup\Storage\Storages\SFTP;

use Exception;
use WPStaging\Framework\Filesystem\FileObject;
use WPStaging\Framework\Queue\FinishedQueueException;
use WPStaging\Pro\Backup\Dto\Job\JobExportDataDto;
use WPStaging\Pro\Backup\Dto\StepsDto;
use WPStaging\Pro\Backup\Exceptions\StorageException;
use WPStaging\Pro\Backup\Storage\RemoteUploaderInterface;
use WPStaging\Pro\Backup\Storage\Storages\SFTP\Auth;
use WPStaging\Vendor\Psr\Log\LoggerInterface;

use function WPStaging\functions\debug_log;

class Uploader implements RemoteUploaderInterface
{
    /** @var StepsDto */
    private $stepsDto;

    /** @var JobExportDataDto */
    private $jobDataDto;

    /** @var LoggerInterface */
    private $logger;

    /** @var string */
    private $filePath;

    /** @var string */
    private $path;

    /** @var string */
    private $remotePath;

    /** @var int */
    private $maxBackupsToKeep;

    /** @var FileObject */
    private $fileObject;

    /** @var int */
    private $chunkSize;

    /** @var Auth */
    private $auth;

    /** @var ClientInterface */
    private $client;

    /** @var bool|string */
    private $error;

    public function __construct(Auth $auth)
    {
        $this->error = false;
        $this->auth = $auth;
        if (!$this->auth->isAuthenticated()) {
            $this->error = __('FTP / SFTP service is not authenticated. Backup is still available locally.', 'wp-staging');
            return;
        }

        $this->client = $auth->getClient();
        $options = $this->auth->getOptions();
        $this->path = !empty($options['location']) ? trailingslashit($options['location']) : '';
        $this->maxBackupsToKeep = isset($options['maxBackupsToKeep']) ? $options['maxBackupsToKeep'] : 0;
        $this->maxBackupsToKeep = intval($this->maxBackupsToKeep);
    }

    public function getProviderName()
    {
        return 'SFTP / FTP';
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
        $this->remotePath = $this->path . $this->fileObject->getBasename();

        if (!$this->client->login()) {
            $this->logger->info('Unable to connect to ' . $this->getProviderName());
            return false;
        }

        if (!$this->stepsDto->getTotal() && !$this->deleteOldestFile()) {
            return false;
        }

        if (!$this->stepsDto->getTotal()) {
            $this->stepsDto->setTotal($this->fileObject->getSize());
            $this->stepsDto->setCurrent(0);

            $this->logger->info('Initiate backup upload to ' . $this->getProviderName());
            return true;
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
        $offset = $this->stepsDto->getCurrent();

        $this->fileObject->fseek($offset);
        $chunk = $this->fileObject->fread($this->chunkSize);

        try {
            $this->client->upload($this->path, $this->fileObject->getBasename(), $chunk, $offset);
            $offset += strlen($chunk);
        } catch (StorageException $ex) {
            throw new StorageException($ex->getMessage());
        } catch (Exception $ex) {
            debug_log("Error: " . $ex->getMessage());
        }

        if ($offset >= $this->fileObject->getSize()) {
            throw new FinishedQueueException();
        }

        $this->stepsDto->setCurrent($offset);

        return $offset;
    }

    public function stopUpload()
    {
        $this->client->close();
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

        if ($this->client === false) {
            $this->error = 'Unable to Initiate a Client';
            return false;
        }

        try {
            $files = $this->client->getFiles($this->path);
            if (!is_array($files)) {
                $this->error = $this->client->getError() . ' - ' . __('Unable to fetch existing backups for cleanup', 'wp-staging');
                return false;
            }

            if (count($files) < $this->maxBackupsToKeep) {
                return true;
            }

            $result = $this->client->deleteFile($files[0]['name']);
            if ($result === false) {
                $this->error = $this->client->getError();
                return false;
            }

            $this->logger->info(__('FTP / SFTP - Deleted oldest backup file', 'wp-staging'));
            return true;
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            return false;
        }
    }
}
