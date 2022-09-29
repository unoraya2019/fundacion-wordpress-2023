<?php

namespace WPStaging\Pro\Backup\Storage;

use WPStaging\Pro\Backup\Dto\Job\JobExportDataDto;
use WPStaging\Pro\Backup\Dto\StepsDto;
use WPStaging\Vendor\Psr\Log\LoggerInterface;

interface RemoteUploaderInterface
{
    public function getProviderName();

    public function setupUpload(LoggerInterface $logger, StepsDto $stepsDto, JobExportDataDto $jobDataDto, $chunkSize = 1 * 1024 * 1024);

    public function setBackupFilePath($backupFilePath);

    public function chunkUpload();

    public function stopUpload();

    public function getError();
}
