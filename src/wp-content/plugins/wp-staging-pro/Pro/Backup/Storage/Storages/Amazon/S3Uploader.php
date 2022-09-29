<?php

namespace WPStaging\Pro\Backup\Storage\Storages\Amazon;

use Exception;
use WPStaging\Framework\Filesystem\FileObject;
use WPStaging\Framework\Queue\FinishedQueueException;
use WPStaging\Pro\Backup\Dto\Job\JobExportDataDto;
use WPStaging\Pro\Backup\Dto\StepsDto;
use WPStaging\Pro\Backup\Storage\RemoteUploaderInterface;
use WPStaging\Pro\Backup\Storage\Storages\Amazon\S3;
use WPStaging\Vendor\Psr\Log\LoggerInterface;

use function WPStaging\functions\debug_log;

class S3Uploader implements RemoteUploaderInterface
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
    private $bucketName;

    /** @var string */
    private $path;

    /** @var string */
    private $objectKey;

    /** @var int */
    private $maxBackupsToKeep;

    /** @var FileObject */
    private $fileObject;

    /** @var int */
    private $chunkSize;

    /** @var S3 */
    private $auth;

    /** @var S3Client */
    private $client;

    /** @var bool|string */
    private $error;

    public function __construct(S3 $auth)
    {
        $this->error = false;
        $this->auth = $auth;

        if (!$this->auth->isGuzzleAvailable()) {
            $this->error = __('cURL extension is missing. Backup is still available locally.', 'wp-staging');
            return;
        }

        if (!$this->auth->isAuthenticated()) {
            $this->error = __('Amazon S3 service is not authenticated. Backup is still available locally.', 'wp-staging');
            return;
        }

        $this->client = $auth->getClient();
        $options = $this->auth->getOptions();
        $location = isset($options['location']) ? $options['location'] : '';
        $location = $this->auth->getLocation($location);
        $this->bucketName = $location[0];
        $this->path = $location[1];
        $this->maxBackupsToKeep = isset($options['maxBackupsToKeep']) ? $options['maxBackupsToKeep'] : 0;
        $this->maxBackupsToKeep = intval($this->maxBackupsToKeep);
    }

    public function getProviderName()
    {
        return 'Amazon S3';
    }

    public function setupUpload(LoggerInterface $logger, StepsDto $stepsDto, JobExportDataDto $jobDataDto, $chunkSize = 5 * 1024 * 1024)
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
        $this->objectKey = $this->path . $this->fileObject->getBasename();

        if (!$this->stepsDto->getTotal() && !$this->deleteOldestFile()) {
            return false;
        }

        if (!$this->stepsDto->getTotal()) {
            $this->stepsDto->setTotal($this->fileObject->getSize());
            $this->stepsDto->setCurrent(0);

            $model = $this->client->createMultipartUpload([
                'Bucket' => $this->bucketName,
                'Key' => $this->objectKey,
                'ContentType' => 'application/octet-stream',
                'Metadata' => []
            ]);

            $this->jobDataDto->setRemoteStorageMeta([
                'UploadId' => $model['UploadId'],
                'Parts' => []
            ]);

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

        $partNumber = (int)ceil(($offset - 1) / $this->chunkSize);
        $partNumber++;

        $this->fileObject->fseek($offset);
        $chunk = $this->fileObject->fread($this->chunkSize);
        $resumeData = $this->jobDataDto->getRemoteStorageMeta();

        try {
            $uploadId = $resumeData['UploadId'];

            $result = $this->client->uploadPart([
                'Bucket' => $this->bucketName,
                'Key' => $this->objectKey,
                'UploadId' => $uploadId,
                'PartNumber' => $partNumber,
                'Body' => $chunk,
            ]);

            $parts = $resumeData['Parts'];
            $parts['Parts'][$partNumber] = [
                'PartNumber' => $partNumber,
                'ETag' => $result['ETag'],
            ];

            $resumeData['Parts'] = $parts;

            $offset += strlen($chunk);

            if ($offset >= $this->fileObject->getSize()) {
                $result = $this->client->completeMultipartUpload([
                    'Bucket' => $this->bucketName,
                    'Key' => $this->objectKey,
                    'UploadId' => $uploadId,
                    'MultipartUpload' => $parts,
                ]);

                throw new FinishedQueueException();
            }
        } catch (Exception $ex) {
            $result = $this->client->abortMultipartUpload([
                'Bucket' => $this->bucketName,
                'Key' => $this->objectKey,
                'UploadId' => $uploadId
            ]);

            debug_log($ex->getMessage());
        }

        $this->jobDataDto->setRemoteStorageMeta($resumeData);
        $this->stepsDto->setCurrent($offset);

        return $offset;
    }

    public function stopUpload()
    {
        // no-op
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
            return false;
        }

        try {
            $contents = $this->client->listObjects([
                'Bucket' => $this->bucketName,
                'Prefix' => $this->path
            ]);

            $objects = $contents['Contents'];

            if ($objects === null) {
                return true;
            }

            if (!is_array($objects)) {
                return true;
            }

            // Filter out directories and objects in subdirectories
            $objects = array_filter($objects, function ($object) {
                $key = str_replace($this->path, '', $object['Key']);

                // Only return direct child objects
                return strpos($key, '/') === false;
            });

            $objects = array_values($objects);

            if (count($objects) < $this->maxBackupsToKeep) {
                return true;
            }

            // Sort by date in ascending order
            uasort($objects, function ($object1, $object2) {
                $date1 = (new \DateTime($object1['LastModified']));
                $date2 = (new \DateTime($object2['LastModified']));

                return $date1 > $date2;
            });

            $fileToDelete = $objects[0];
            $this->client->deleteObject([
                'Bucket' => $this->bucketName,
                'Key'    => $fileToDelete['Key']
            ]);

            $this->logger->info(__('Amazon S3 - Deleted oldest backup file', 'wp-staging'));
            return true;
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            return false;
        }
    }
}
