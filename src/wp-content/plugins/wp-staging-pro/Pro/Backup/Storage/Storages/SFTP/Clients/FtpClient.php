<?php

namespace WPStaging\Pro\Backup\Storage\Storages\SFTP\Clients;

use Exception;

use function WPStaging\functions\debug_log;

class FtpClient implements ClientInterface
{
    /** @var resource */
    protected $ftp;

    /** @var string */
    protected $username;

    /** @var string */
    protected $password;

    /** @var bool */
    protected $passive;

    /** @var string|false */
    protected $error;

    /**
     * @var string $host
     * @var string $username
     * @var string $password
     * @var bool   $ssl
     * @var bool   $passive
     * @var int    $port
     *
     * @throws FtpException
     */
    public function __construct($host, $username, $password, $ssl, $passive, $port)
    {
        if (!extension_loaded('ftp')) {
            throw new FtpException("PHP FTP extension not loaded");
        }

        $this->username = $username;
        $this->password = $password;
        $this->passive = $passive;
        if ($ssl) {
            $this->ftp = ftp_ssl_connect($host, $port, 90);
            return;
        }

        $this->ftp = ftp_connect($host, $port, 90);
    }

    public function login()
    {
        $result = ftp_login($this->ftp, $this->username, $this->password);

        if ($result === false) {
            return false;
        }

        ftp_pasv($this->ftp, $this->passive);
        ftp_set_option($this->ftp, FTP_AUTOSEEK, false);

        return true;
    }

    public function upload($remotePath, $file, $chunk, $offset = 0)
    {
        $result = false;
        if (($handle = fopen('php://temp', 'wb+'))) {
            if (($fileSize = fwrite($handle, $chunk))) {
                rewind($handle);
            }

            if ($remotePath !== '') {
                $remotePath = trailingslashit($remotePath);
            }

            try {
                $result = @ftp_fput($this->ftp, $remotePath . $file, $handle, FTP_BINARY, $offset);
            } catch (Exception $e) {
                debug_log("Error: " . $e->getMessage());
            }

            fclose($handle);
        }

        return $result;
    }

    public function close()
    {
        @ftp_close($this->ftp);
    }

    public function getError()
    {
        return $this->error;
    }

    /**
     * @param string $path
     *
     * @return array|false
     */
    public function getFiles($path)
    {
        if ($path !== '') {
            ftp_chdir($this->ftp, $path);
        }

        $items = [];
        try {
            $items = ftp_rawlist($this->ftp, '-tr');
        } catch (Exception $ex) {
            return false;
        }

        $files = [];
        if (!is_array($items)) {
            return [];
        }

        foreach ($items as $item) {
            if (empty($item)) {
                continue;
            }

            $metas = explode(' ', $item);

            $files[] = [
                'time' => null,
                'name' => $metas[count($metas) - 1]
            ];
        }

        return $files;
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    public function deleteFile($path)
    {
        try {
            return ftp_delete($this->ftp, $path);
        } catch (Exception $ex) {
            return false;
        }
    }
}
