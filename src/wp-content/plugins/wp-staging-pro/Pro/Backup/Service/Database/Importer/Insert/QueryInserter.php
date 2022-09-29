<?php

namespace WPStaging\Pro\Backup\Service\Database\Importer\Insert;

use WPStaging\Framework\Adapter\Database;
use WPStaging\Framework\Adapter\Database\WpDbAdapter;
use WPStaging\Framework\Adapter\Database\InterfaceDatabaseClient;
use WPStaging\Framework\Traits\ResourceTrait;
use WPStaging\Pro\Backup\Dto\Job\JobImportDataDto;
use WPStaging\Vendor\Psr\Log\LoggerInterface;

abstract class QueryInserter
{
    use ResourceTrait;

    protected $wpdb;

    protected $client;

    protected $logger;

    protected $jobImportDataDto;

    protected $maxAllowedPacket;

    protected $maxInnoDbLogSize;

    protected $error = false;

    public function initialize(Database $database, JobImportDataDto $jobImportDataDto, LoggerInterface $logger)
    {
        $this->client = $database->getClient();
        $this->wpdb = $database->getWpdba();
        $this->jobImportDataDto = $jobImportDataDto;
        $this->logger = $logger;

        $this->setMaxAllowedPackage();
        $this->setInnoDbLogFileSize();
    }

    abstract public function processQuery(&$insertQuery);

    abstract public function commit();

    protected function exec(&$query)
    {
        $result = $this->client->query($query);

        return $result !== false;
    }

    protected function setMaxAllowedPackage()
    {
        try {
            $result = $this->client->query("SHOW VARIABLES LIKE 'max_allowed_packet'");
            $row = $this->client->fetchAssoc($result);

                $this->client->freeResult($result);

            $maxAllowedPacket = $this->getNumberFromResult($row);

                $maxAllowedPacket = max(16 * KB_IN_BYTES, 0.9 * $maxAllowedPacket);

                $maxAllowedPacket = min(64 * MB_IN_BYTES, $maxAllowedPacket);
        } catch (\Exception $e) {
                $maxAllowedPacket = (1 * MB_IN_BYTES) * 0.9;
        }

        $maxAllowedPacket = apply_filters('wpstg.import.database.maxAllowedPacket', $maxAllowedPacket);

        $this->maxAllowedPacket = (int)$maxAllowedPacket;
    }

    protected function setInnoDbLogFileSize()
    {
        try {
                                                                                    $innoDbLogFileSize = $this->wpdb->getClient()->get_row("SHOW VARIABLES LIKE 'innodb_log_file_size';", ARRAY_A);
            $innoDbLogFileSize = $this->getNumberFromResult($innoDbLogFileSize);

                                                                        $innoDbLogFileGroups = $this->wpdb->getClient()->get_row("SHOW VARIABLES LIKE 'innodb_log_files_in_group';", ARRAY_A);
            $innoDbLogFileGroups = $this->getNumberFromResult($innoDbLogFileGroups);

            $innoDbLogSize = $innoDbLogFileSize * $innoDbLogFileGroups;

                        $innoDbLogSize = max(1 * MB_IN_BYTES, $innoDbLogSize * 0.9);

                        $innoDbLogSize = min(64 * MB_IN_BYTES, $innoDbLogSize);
        } catch (\Exception $e) {
                        $innoDbLogSize = 9 * MB_IN_BYTES;
        }

        $innoDbLogSize = apply_filters('wpstg.import.database.innoDbLogSize', $innoDbLogSize);

        $this->maxInnoDbLogSize = (int)$innoDbLogSize;
    }

    private function getNumberFromResult($result)
    {
        if (
            is_array($result) &&
            array_key_exists('Value', $result) &&
            is_numeric($result['Value']) &&
            (int)$result['Value'] > 0
        ) {
            return (int)$result['Value'];
        } else {
            throw new \UnexpectedValueException();
        }
    }

    public function getLastError()
    {
        return $this->error;
    }

    protected function doQueryExceedsMaxAllowedPacket($query)
    {
        $this->error = false;
        if (strlen($query) >= $this->maxAllowedPacket) {
            $this->error = sprintf(
                'Query: "%s" was skipped because it exceeded the maximum size. Query size: %s | max_allowed_packet: %s. Follow this link: %s for more detail',
                substr($query, 0, 1000) . '...',
                size_format(strlen($query)),
                size_format($this->maxAllowedPacket),
                'https://wp-staging.com/docs/increase-max_allowed_packet-size-in-mysql/'
            );

            return true;
        }

        return false;
    }
}
