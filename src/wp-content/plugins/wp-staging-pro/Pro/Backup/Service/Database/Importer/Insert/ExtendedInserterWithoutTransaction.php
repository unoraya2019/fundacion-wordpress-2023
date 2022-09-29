<?php

namespace WPStaging\Pro\Backup\Service\Database\Importer\Insert;

use function WPStaging\functions\debug_log;

class ExtendedInserterWithoutTransaction extends QueryInserter
{
    protected $extendedQuery = '';

    public function processQuery(&$queryToInsert)
    {
        if ($this->doQueryExceedsMaxAllowedPacket($queryToInsert)) {
            return null;
        }

        $this->extendInsert($queryToInsert);

        if (strlen($this->extendedQuery) >= $this->maxAllowedPacket) {
            return $this->execExtendedQuery();
        }

        return null;
    }

    public function commit()
    {
        $this->execExtendedQuery();
    }

    public function execExtendedQuery()
    {
        if (empty($this->extendedQuery)) {
            return null;
        }

        $this->extendedQuery .= ';';

        $success = $this->exec($this->extendedQuery);

        if ($success) {
            $this->extendedQuery = '';
            $this->jobImportDataDto->setTableToImport('');
            return true;
        } else {
            $this->showError();
            $this->extendedQuery = '';
            $this->jobImportDataDto->setTableToImport('');
            return false;
        }
    }

    protected function showError()
    {

        switch ($this->client->errno()) {
            case 1153:
            case 2006:
                $this->logger->warning(__('The error message means got a packet bigger than max_allowed_packet bytes.', 'wp-staging'));
                break;
            case 1030:
                $this->logger->warning(__('Engine changed to InnoDB, as it your MySQL server does not support MyISAM.', 'wp-staging'));
                break;
            case 1071:
            case 1709:
                $this->logger->warning(__('Row format changed to DYNAMIC, as it would exceed the maximum length according to your MySQL settings. To not see this message anymore, please upgrade your MySQL version or increase the row format.', 'wp-staging'));
                break;
            case 1214:
                $this->logger->warning(__('FULLTEXT removed from query, as your current MySQL version does not support it. To not see this message anymore, please upgrade your MySQL version.', 'wp-staging'));
                break;
            case 1226:
                if (stripos($this->client->error(), 'max_queries_per_hour') !== false) {
                    $this->logger->warning(__(sprintf('Your server has reached the maximum allowed queries per hour set by your admin or hosting provider. Please increase MySQL max_queries_per_hour_limit. <a href="https://wp-staging.com/docs/mysql-database-error-codes/" target="_blank">Technical details</a>'), 'wp-staging'));
                } elseif (stripos($this->client->error(), 'max_updates_per_hour') !== false) {
                    $this->logger->warning(__(sprintf('Your server has reached the maximum allowed updates per hour set by your admin or hosting provider. Please increase MySQL max_updates_per_hour. <a href="https://wp-staging.com/docs/mysql-database-error-codes/" target="_blank">Technical details</a>'), 'wp-staging'));
                } elseif (stripos($this->client->error(), 'max_connections_per_hour') !== false) {
                    $this->logger->warning(__(sprintf('Your server has reached the maximum allowed connections per hour set by your admin or hosting provider. Please increase MySQL max_connections_per_hour. <a href="https://wp-staging.com/docs/mysql-database-error-codes/" target="_blank">Technical details</a>'), 'wp-staging'));
                } elseif (stripos($this->client->error(), 'max_user_connections') !== false) {
                    $this->logger->warning(__(sprintf('Your server has reached the maximum allowed connections per hour set by your admin or hosting provider. Please increase MySQL max_user_connections. <a href="https://wp-staging.com/docs/mysql-database-error-codes/" target="_blank">Technical details</a>'), 'wp-staging'));
                }
                break;
            case 1813:
                $this->logger->warning(__('Could not import the database. MySQL returned the error code 1813, which is related to a tablespace error that WP STAGING can\'t handle. Please contact your hosting company.', 'wp-staging'));
        }

        if (defined('WPSTG_DEBUG') && WPSTG_DEBUG) {
            $this->logger->warning(__(sprintf('ExtendedInserterWithoutTransaction Failed Query: %s', substr($this->extendedQuery, 0, 1000)), 'wp-staging'));
            debug_log(__(sprintf('ExtendedInserterWithoutTransaction Failed Query: %s', substr($this->extendedQuery, 0, 1000)), 'wp-staging'));
        }

        $this->logger->warning(__(sprintf('Could not import the query. MySQL has returned the error code %d, with message "%s". If this issue persists, try using the same MySQL version used to create this Backup (%s).', $this->client->errno(), $this->client->error(), $this->jobImportDataDto->getBackupMetadata()->getSqlServerVersion())));
    }

    protected function extendInsert(&$insertQuery)
    {
        preg_match('#^INSERT INTO `(.+?(?=`))` VALUES (\(.+\));$#', $insertQuery, $matches);

        if (count($matches) !== 3) {
            throw new \Exception("Skipping INSERT query: $insertQuery");
        }

                $insertingIntoTableName = $matches[1];

        $insertingIntoHeader = "INSERT INTO `$insertingIntoTableName` VALUES ";

        $isFirstValue = false;

        if (empty($this->jobImportDataDto->getTableToImport())) {
            if (!empty($this->extendedQuery)) {
                throw new \UnexpectedValueException('Query is not empty, cannot proceed.');
            }

            $this->jobImportDataDto->setTableToImport($insertingIntoTableName);
            $this->extendedQuery .= $insertingIntoHeader;
            $isFirstValue = true;
        } elseif ($this->jobImportDataDto->getTableToImport() !== $insertingIntoTableName) {
            $this->execExtendedQuery();
            if (!empty($this->extendedQuery)) {
                throw new \UnexpectedValueException('Query is not empty, cannot proceed.');
            }
            $this->jobImportDataDto->setTableToImport($insertingIntoTableName);
            $this->extendedQuery .= $insertingIntoHeader;
            $isFirstValue = true;
        }

        if (!$isFirstValue && strlen($this->extendedQuery . ",$matches[2]") >= $this->maxAllowedPacket) {
            $this->execExtendedQuery();
            if (!empty($this->extendedQuery)) {
                throw new \UnexpectedValueException('Query is not empty, cannot proceed.');
            }

            $this->jobImportDataDto->setTableToImport($insertingIntoTableName);
            $this->extendedQuery .= $insertingIntoHeader;
            $isFirstValue = true;
        }

        if ($isFirstValue) {
            $this->extendedQuery .= $matches[2];
        } else {
            $this->extendedQuery .= ",$matches[2]";
        }
    }
}
