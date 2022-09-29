<?php

namespace WPStaging\Pro\Backup\Service\Database\Importer\Insert;

use function WPStaging\functions\debug_log;

class ExtendedInserter extends TransactionInserter
{
    protected $extendedQuery = '';

    public function processQuery(&$queryToInsert)
    {
        if ($this->doQueryExceedsMaxAllowedPacket($queryToInsert)) {
            return null;
        }

        $this->maybeStartTransaction();

        $this->extendInsert($queryToInsert);

        if (strlen($this->extendedQuery) > $this->maxAllowedPacket) {
            $this->execExtendedQuery();
        }

        $this->maybeCommit();
    }

    public function commit()
    {
        if (empty($this->extendedQuery)) {
            return;
        }

        $this->maybeStartTransaction();

        $this->execExtendedQuery(true);

        parent::commit();
    }

    public function execExtendedQuery($isCommitting = false)
    {
        if (empty($this->extendedQuery)) {
            return;
        }

        $this->extendedQuery .= ';';

        $success = $this->exec($this->extendedQuery);

        if ($success) {
            $this->currentTransactionSize += strlen($this->extendedQuery);

            $this->extendedQuery = '';
            $this->jobImportDataDto->setTableToImport('');
        } else {
            if (defined('WPSTG_DEBUG') && WPSTG_DEBUG) {
                $query = substr($this->extendedQuery, 0, 1000);
                debug_log("Extended Inserter Failed Query: {$query}");
            }

            $this->extendedQuery = '';
            $this->jobImportDataDto->setTableToImport('');

            if (!$isCommitting) {
                $this->commit();
            }

            throw new \RuntimeException(sprintf(
                'Failed to insert extended query. Query: %s Reason Code: %s Reason Message: %s',
                $this->extendedQuery,
                $this->client->errno(),
                $this->client->error()
            ));
        }
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
        } else {
            if ($this->jobImportDataDto->getTableToImport() !== $insertingIntoTableName) {
                $this->commit();
                if (!empty($this->extendedQuery)) {
                    throw new \UnexpectedValueException('Query is not empty, cannot proceed.');
                }
                $this->jobImportDataDto->setTableToImport($insertingIntoTableName);
                $this->extendedQuery .= $insertingIntoHeader;
                $isFirstValue = true;
            }
        }

        if ($isFirstValue) {
            $this->extendedQuery .= $matches[2];
        } else {
            $this->extendedQuery .= ",$matches[2]";
        }
    }
}
