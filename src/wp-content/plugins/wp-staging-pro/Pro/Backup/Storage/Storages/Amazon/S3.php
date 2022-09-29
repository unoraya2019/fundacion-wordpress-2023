<?php

namespace WPStaging\Pro\Backup\Storage\Storages\Amazon;

use WPStaging\Pro\Backup\Storage\AbstractStorage;
use WPStaging\Vendor\Aws\S3\S3Client;
use Exception;
use WPStaging\Framework\Utils\Sanitize;

use function WPStaging\functions\debug_log;

class S3 extends AbstractStorage
{
    const S3_VERSION = '2006-03-01';

    /** @var Sanitize */
    protected $sanitize;

    public function __construct(Sanitize $sanitize)
    {
        $this->identifier = 'amazons3';
        $this->label = 'Amazon S3';
        $this->sanitize = $sanitize;
    }

    public function authenticate()
    {
        // no-op
    }

    /**
     * @return bool
     */
    public function testConnection()
    {
        try {
            $accessKey = isset($_POST['access_key']) ? $this->sanitize->sanitizeString($_POST['access_key']) : '';
            $secretKey = isset($_POST['secret_key']) ? $this->sanitize->sanitizeString($_POST['secret_key']) : '';
            $region = isset($_POST['region']) ? $this->sanitize->sanitizeString($_POST['region']) : '';
            // Instantiate the S3 client with your AWS credentials
            $s3Client = new S3Client($this->getConfigOptions($accessKey, $secretKey, $region));

            $buckets = $s3Client->listBuckets();
        } catch (Exception $ex) {
            debug_log("S3 Client : " . $ex->getMessage());
            return false;
        }

        return true;
    }

    /** @return S3Client|false */
    public function getClient($options = null)
    {
        if ($options === null) {
            $options = $this->getOptions();
        }

        try {
            // Instantiate the S3 client with your AWS credentials
            $s3Client = new S3Client($this->getConfigOptions($options['accessKey'], $options['secretKey'], $options['region']));
        } catch (Exception $ex) {
            debug_log($ex->getMessage());
            return false;
        }

        return $s3Client;
    }

    /**
     * @param array $settings
     * @return bool
     */
    public function updateSettings($settings)
    {
        $options = $this->getOptions();
        $accessKey = isset($settings['access_key']) ? $this->sanitize->sanitizeString($settings['access_key']) : '';
        $secretKey = isset($settings['secret_key']) ? $this->sanitize->sanitizeString($settings['secret_key']) : '';
        $region = isset($settings['region']) ? $this->sanitize->sanitizeString($settings['region']) : '';
        $location = isset($settings['location']) ? $this->sanitize->sanitizeURL($settings['location']) : '';
        $backupsToKeep = isset($settings['max_backups_to_keep']) ? $this->sanitize->sanitizeInt($settings['max_backups_to_keep']) : 2;
        // Earlt bail if options already the same
        if (
            array_key_exists('accessKey', $options) &&
            $options['accessKey'] === $accessKey &&
            $options['secretKey'] === $secretKey &&
            $options['region'] === $region &&
            $options['location'] === $location &&
            $options['maxBackupsToKeep'] === $backupsToKeep
        ) {
            return true;
        }

        $options['location'] = $location;
        $options['region'] = $region;
        $options['accessKey'] = $accessKey;
        $options['secretKey'] = $secretKey;
        $options['maxBackupsToKeep'] = $backupsToKeep;

        $options['isAuthenticated'] = false;

        $client = $this->getClient($options);
        if ($client !== false) {
            $options['isAuthenticated'] = true;
        }

        return $this->saveOptions($options);
    }

    /**
     * Revoke both access and refresh token,
     * Also unauthenticate the provider
     */
    public function revoke()
    {
        $options = $this->getOptions();

        // Early bail if already unauthenticated
        if ($options['isAuthenticated'] === false) {
            return true;
        }

        $options['isAuthenticated'] = false;
        $options['accessKey'] = '';
        $options['secretKey'] = '';
        $options['region']    = '';
        $options['location']  = '';

        return parent::saveOptions($options);
    }

    /**
     * Delete all backup files
     * Used by /tests/webdriverNew/Backup/AmazonS3UploadCest.php
     * @return void
     */
    public function cleanBackups()
    {
        $options = $this->getOptions();
        $client = $this->getClient($options);
        if ($client === false) {
            return;
        }

        $bucketName = $this->explodeLocation($options['location'])[0];

        try {
            $result = $client->listObjects([
                'Bucket' => $bucketName,
            ]);

            $objects = [];
            foreach ($result['Contents'] as $object) {
                $objects[] = [
                    'Key' => $object['Key']
                ];
            }

            $client->deleteObjects([
                'Bucket' => $bucketName,
                'Delete' => [
                    'Objects' => $objects,
                ],
            ]);
        } catch (Exception $ex) {
            return;
        }
    }

    /**
     * @param $backupFile
     * @return bool
     */
    public function isBackupUploaded($backupFile)
    {
        $options = $this->getOptions();
        $client = $this->getClient($options);
        if ($client === false) {
            return false;
        }

        $bucketName = $this->explodeLocation($options['location'])[0];

        try {
            $result = $client->listObjects([
                'Bucket' => $bucketName,
            ]);

            foreach ($result['Contents'] as $object) {
                if ($backupFile === $object['Key']) {
                    return true;
                }
            }
        } catch (Exception $ex) {
            debug_log($ex->getMessage());
        }
        debug_log('Could not find backup. Error: ' . $bucketName);
        return false;
    }

    /** @return array Bucket Name of 0 index and rest of path on 1 index */
    public function getLocation()
    {
        $options = $this->getOptions();
        $location = $this->explodeLocation($options['location']);
        $bucketName = $location[0];
        $path = '';
        for ($i = 1; $i < count($location); $i++) {
            $path .= $location[$i] . '/';
        }

        return [$bucketName, $path];
    }

    /**
     * Get configuration object
     *
     * @return array
     */
    protected function getConfigOptions($accessKey, $secretKey, $region)
    {
        return [
            'version'     => self::S3_VERSION,
            'region'      => $region,
            'http'        => [
                'verify' => WPSTG_PLUGIN_DIR . 'Pro/Backup/cacert.pem'
            ],
            'credentials' => [
                'key'    => $accessKey,
                'secret' => $secretKey,
            ],
        ];
    }

    private function explodeLocation($location)
    {
        return explode('/', $location);
    }
}
