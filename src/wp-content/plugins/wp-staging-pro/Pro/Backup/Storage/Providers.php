<?php

namespace WPStaging\Pro\Backup\Storage;

use WPStaging\Pro\Backup\Storage\Storages\Amazon\S3;
use WPStaging\Pro\Backup\Storage\Storages\GoogleDrive\Auth as GoogleDriveAuth;
use WPStaging\Pro\Backup\Storage\Storages\SFTP\Auth as SftpAuth;

class Providers
{
    protected $storages = [];

    public function __construct(GoogleDriveAuth $googleAuth, S3 $amazonS3, SftpAuth $sftpAuth)
    {
        $this->storages = [
            [
                'id'   => 'googleDrive',
                'name' => esc_html__('Google Drive'),
                'enabled' => true,
                'activated' => $googleAuth->isAuthenticated(),
                'settingsPath'  => $this->getStorageAdminPage('googleDrive'),
                'authClass' => GoogleDriveAuth::class
            ],
            [
                'id'   => 'amazonS3',
                'name' => esc_html__('Amazon S3'),
                'enabled' => true,
                'activated' => $amazonS3->isAuthenticated(),
                'settingsPath'  => $this->getStorageAdminPage('amazonS3'),
                'authClass' => S3::class
            ],
            [
                'id'   => 'dropbox',
                'name' => esc_html__('Dropbox'),
                'enabled' => false,
                'activated' => false,
                'settingsPath'  => $this->getStorageAdminPage('dropbox'),
                'authClass' => ''
            ],
            [
                'id'   => 'oneDrive',
                'name' => esc_html__('One Drive'),
                'enabled' => false,
                'activated' => false,
                'settingsPath'  => $this->getStorageAdminPage('onedrive'),
                'authClass' => ''
            ],
            [
                'id'   => 'sftp',
                'name' => esc_html__('FTP / SFTP'),
                'enabled' => true,
                'activated' => $sftpAuth->isAuthenticated(),
                'settingsPath'  => $this->getStorageAdminPage('sftp'),
                'authClass' => SftpAuth::class
            ]
        ];
    }

    /**
     * @param null|bool $isEnabled. Default null
     *                  Use null for all storages,
     *                  Use true for enabled storages,
     *                  Use false for disabled storages
     *
     * @return array
     */
    public function getStorageIds($isEnabled = null)
    {
        return array_map(function ($storage) {
            return $storage['id'];
        }, $this->getStorages($isEnabled));
    }

    /**
     * @param null|bool $isEnabled. Default null
     *                  Use null for all storages,
     *                  Use true for enabled storages,
     *                  Use false for disabled storages
     *
     * @return array
     */
    public function getStorages($isEnabled = null)
    {
        if ($isEnabled === null) {
            return $this->storages;
        }

        return array_filter($this->storages, function ($storage) use ($isEnabled) {
            return $storage['enabled'] === $isEnabled;
        });
    }

    /**
     * @param string $id
     * @param string $property
     * @param null|bool $isEnabled. Default null
     *                  Use null for all storages,
     *                  Use true for enabled storages,
     *                  Use false for disabled storages
     *
     * @return mixed
     */
    public function getStorageProperty($id, $property, $isEnabled = null)
    {
        foreach ($this->getStorages($isEnabled) as $storage) {
            if ($storage['id'] === $id) {
                if (array_key_exists($property, $storage)) {
                    return $storage[$property];
                }
            }
        }

        return false;
    }

    private function getStorageAdminPage($storageTab)
    {
        return admin_url('admin.php?page=wpstg-settings&tab=remote-storages&sub=' . $storageTab);
    }
}
