<?php

// TODO PHP7.x; declare(strict_type=1);
// TODO PHP7.x; type hints & return types
// TODO PHP7.1; constant visibility

namespace WPStaging\Pro\Backup\Task\Tasks\JobExport;

use Exception;
use WPStaging\Core\WPStaging;
use WPStaging\Framework\Adapter\Directory;
use WPStaging\Framework\Filesystem\DiskWriteCheck;
use WPStaging\Framework\Filesystem\Filesystem;
use WPStaging\Framework\Filesystem\FilterableDirectoryIterator;
use WPStaging\Framework\Filesystem\PathIdentifier;
use WPStaging\Framework\Queue\SeekableQueueInterface;
use WPStaging\Pro\Backup\Dto\StepsDto;
use WPStaging\Pro\Backup\Exceptions\DiskNotWritableException;
use WPStaging\Pro\Backup\Task\ExportTask;
use WPStaging\Vendor\Psr\Log\LoggerInterface;
use WPStaging\Framework\Queue\FinishedQueueException;
use WPStaging\Framework\Utils\Cache\Cache;

class FilesystemScannerTask extends ExportTask
{
    /** @var Directory */
    protected $directory;

    /** @var Filesystem */
    protected $filesystem;

    /** @var SeekableQueueInterface */
    protected $compressorQueue;

    /** @var PathIdentifier */
    protected $pathIdentifier;

    protected $ignoreFileExtensions;
    protected $ignoreFileBiggerThan;
    protected $ignoreFileExtensionFilesBiggerThan;

    public function __construct(Directory $directory, PathIdentifier $pathIdentifier, LoggerInterface $logger, Cache $cache, StepsDto $stepsDto, SeekableQueueInterface $taskQueue, SeekableQueueInterface $compressorQueue, Filesystem $filesystem)
    {
        parent::__construct($logger, $cache, $stepsDto, $taskQueue);

        $this->directory = $directory;
        $this->filesystem = $filesystem;
        $this->pathIdentifier = $pathIdentifier;
        $this->compressorQueue = $compressorQueue;
    }

    public static function getTaskName()
    {
        return 'backup_export_filesystem_scan';
    }

    public static function getTaskTitle()
    {
        return 'Discovering Files';
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $this->compressorQueue->setup(FileExportTask::getTaskName(), SeekableQueueInterface::MODE_WRITE);
        $this->setupFilters();
        $this->setupFilesystemScanner();

        if ($this->stepsDto->getCurrent() === 0) {
            $this->scanWpContentDirectory();
            $this->unlockQueue();
            return $this->generateResponse(true);
        }
        if ($this->stepsDto->getCurrent() === 1) {
            $this->scanPluginsDirectories();
            $this->unlockQueue();
            return $this->generateResponse(true);
        }
        if ($this->stepsDto->getCurrent() === 2) {
            $this->scanMuPluginsDirectory();
            $this->unlockQueue();
            return $this->generateResponse(true);
        }
        if ($this->stepsDto->getCurrent() === 3) {
            $this->scanThemesDirectory();
            $this->unlockQueue();
            return $this->generateResponse(true);
        }
        if ($this->stepsDto->getCurrent() === 4) {
            $this->scanUploadsDirectory();
            $this->unlockQueue();
            return $this->generateResponse(true);
        }

        while (!$this->isThreshold() && !$this->stepsDto->isFinished()) {
            $this->scan();
        }

        if ($this->stepsDto->isFinished()) {
            $this->stepsDto->setManualPercentage(100);
            $this->logger->info(__(sprintf('Finished discovering Files. (%d files)', $this->jobDataDto->getDiscoveredFiles()), 'wp-staging'));
        } else {
            $this->jobDataDto->setDiscoveringFilesRequests($this->jobDataDto->getDiscoveringFilesRequests() + 1);

            // The manual percentage increments 30% per request, until it hits 90%, point of which it increments 1%
            if ($this->jobDataDto->getDiscoveringFilesRequests() <= 3) {
                // 30%, 60%, 90%...
                $manualPercentage = $this->jobDataDto->getDiscoveringFilesRequests() * 30;
            } elseif ($this->jobDataDto->getDiscoveringFilesRequests() >= 4 && $this->jobDataDto->getDiscoveringFilesRequests() <= 14) {
                // 91%, 92%, 93%...
                $manualPercentage = 90;
                $manualPercentage += $this->jobDataDto->getDiscoveringFilesRequests() - 3;
            } else {
                // 99%
                $manualPercentage = 99;
            }

            $this->stepsDto->setManualPercentage(min($manualPercentage, 100));
            $this->logger->info(__(sprintf('Discovering Files (%d files)', $this->jobDataDto->getDiscoveredFiles()), 'wp-staging'));
        }

        return $this->generateResponse(false);
    }

    protected function setupFilters()
    {
        /**
         * Allow user to exclude certain file extensions from being exported.
         */
        $this->ignoreFileExtensions = (array)apply_filters('wpstg.export.files.ignore.file_extension', [
            'log',
        ]);

        /**
         * Allow user to exclude files larger than given size from being exported.
         */
        $this->ignoreFileBiggerThan = (int)apply_filters('wpstg.export.files.ignore.file_bigger_than', 200 * MB_IN_BYTES);

        /**
         * Allow user to exclude files with extension larger than given size from being exported.
         */
        $this->ignoreFileExtensionFilesBiggerThan = (array)apply_filters('wpstg.export.files.ignore.file_extension_bigger_than', [
            'zip' => 10 * MB_IN_BYTES,
        ]);

        // Allows us to use isset for performance
        $this->ignoreFileExtensions = array_flip($this->ignoreFileExtensions);
    }

    protected function scan()
    {
        try {
            $path = $this->taskQueue->dequeue();

            if (is_null($path)) {
                throw new FinishedQueueException('Directory Scanner Queue is Finished');
            }

            if (empty($path)) {
                return;
            }

            $path = untrailingslashit($this->filesystem->normalizePath($path, true));

            if (!file_exists($path)) {
                throw new Exception("$path is not a directory. Skipping...");
            }

            $this->recursivePathScanning($path);
        } catch (FinishedQueueException $e) {
            try {
                WPStaging::make(DiskWriteCheck::class)->checkPathCanStoreEnoughBytes($this->directory->getPluginUploadsDirectory(), $this->jobDataDto->getFilesystemSize());
            } catch (DiskNotWritableException $e) {
                throw $e;
            } catch (\RuntimeException $e) {
                // soft error, no action needed, but log
                $this->logger->debug($e->getMessage());
            }

            $this->stepsDto->finish();

            return;
        } catch (\OutOfBoundsException $e) {
            $this->logger->debug($e->getMessage());
        } catch (Exception $e) {
            $this->logger->warning($e->getMessage());
        }
    }

    protected function setupFilesystemScanner()
    {
        if ($this->stepsDto->getTotal() > 0) {
            return;
        }

        $excludedDirs = [];

        $excludedDirs[] = WPSTG_PLUGIN_DIR;
        $excludedDirs[] = $this->directory->getPluginUploadsDirectory();
        $excludedDirs[] = trailingslashit(WP_CONTENT_DIR) . 'cache';

        /**
         * @see https://wordpress.org/plugins/all-in-one-wp-migration/
         *      This folder contains backups generated by All In One WP Migration.
         */
        $excludedDirs[] = trailingslashit(WP_CONTENT_DIR) . 'ai1wm-backups';

        /**
         * @see https://wordpress.org/plugins/robin-image-optimizer/
         *      This folder contains a duplicate of the uploads folder, for optimized images.
         *      It can be manually re-generated from the existing media library later.
         */
        $excludedDirs[] = $this->directory->getUploadsDirectory() . 'wio_backup';

        /**
         * Allow user to filter the excluded directories in a site backup.
         *
         * @param array $excludedDirectories
         *
         * @return array An array of directories to exclude.
         */
        $excludedDirs = (array)apply_filters('wpstg.backup.exclude.directories', $excludedDirs);

        $excludedDirs = array_map(function ($path) {
            return $this->filesystem->normalizePath($path, true);
        }, $excludedDirs);

        $this->jobDataDto->setExcludedDirectories($excludedDirs);

        // Browsers will do mime type sniffing on download. Adding binary to header avoids parsing as text/plain and forces download.
        $this->enqueueFileInExport(new \SplFileInfo(WPSTG_PLUGIN_DIR . 'Pro/Backup/wpstgBackupHeader.txt'));

        $this->stepsDto->setTotal(6);
        $this->taskQueue->seek(0);
    }

    protected function scanWpContentDirectory()
    {
        if (!$this->jobDataDto->getIsExportingOtherWpContentFiles()) {
            return;
        }

        // wp-content root
        $wpContentIt = new \DirectoryIterator($this->directory->getWpContentDirectory());

        foreach ($wpContentIt as $otherFiles) {
            // Early bail: We don't touch links
            if ($otherFiles->isLink() || $this->isDot($otherFiles)) {
                continue;
            }

            // Handle files at root level of wp-content
            if ($otherFiles->isFile()) {
                $this->enqueueFileInExport($otherFiles);
                continue;
            }

            if ($otherFiles->isDir()) {
                if (!in_array($this->filesystem->normalizePath($otherFiles->getPathname(), true), $this->directory->getDefaultWordPressFolders())) {
                    $this->enqueueDirToBeScanned($otherFiles);
                    continue;
                }
            }
        }
    }

    protected function scanPluginsDirectories()
    {
        if (!$this->jobDataDto->getIsExportingPlugins()) {
            return;
        }

        $pluginsIt = new \DirectoryIterator($this->directory->getPluginsDirectory());

        foreach ($pluginsIt as $plugin) {
            // Early bail: We don't touch links
            if ($plugin->isLink() || $this->isDot($plugin)) {
                continue;
            }

            if ($plugin->isFile()) {
                $this->enqueueFileInExport($plugin);
            }

            if ($plugin->isDir()) {
                $this->enqueueDirToBeScanned($plugin);
            }
        }
    }

    protected function scanMuPluginsDirectory()
    {
        if (!$this->jobDataDto->getIsExportingMuPlugins()) {
            return;
        }

        $muPluginsIt = new \DirectoryIterator($this->directory->getMuPluginsDirectory());

        /** @var \SplFileInfo $muPlugin */
        foreach ($muPluginsIt as $muPlugin) {
            // Early bail: We don't touch links
            if ($muPlugin->isLink() || $this->isDot($muPlugin)) {
                continue;
            }

            if ($muPlugin->isFile()) {
                if ($muPlugin->getBasename() === 'wp-staging-optimizer.php') {
                    continue;
                }

                $this->enqueueFileInExport($muPlugin);
            }

            if ($muPlugin->isDir()) {
                $this->enqueueDirToBeScanned($muPlugin);
            }
        }
    }

    protected function scanThemesDirectory()
    {
        if (!$this->jobDataDto->getIsExportingThemes()) {
            return;
        }

        foreach ($this->directory->getAllThemesDirectories() as $themesDirectory) {
            $themesIt = new \DirectoryIterator($themesDirectory);

            foreach ($themesIt as $theme) {
                // Early bail: We don't touch links
                if ($theme->isLink() || $this->isDot($theme)) {
                    continue;
                }

                if ($theme->isFile()) {
                    $this->enqueueFileInExport($theme);
                }

                if ($theme->isDir()) {
                    $this->enqueueDirToBeScanned($theme);
                }
            }
        }
    }

    protected function scanUploadsDirectory()
    {
        if (!$this->jobDataDto->getIsExportingUploads()) {
            return;
        }

        $uploadsIt = new \DirectoryIterator($this->directory->getUploadsDirectory());

        foreach ($uploadsIt as $uploadItem) {
            // Early bail: We don't touch links
            if ($uploadItem->isLink() || $this->isDot($uploadItem)) {
                continue;
            }

            if ($uploadItem->isFile()) {
                $this->enqueueFileInExport($uploadItem);
            } elseif ($uploadItem->isDir()) {
                /*
                 * This is a default WordPress year-month uploads folder.
                 *
                 * Here we break down the uploads folder by months, considering it's often the largest folder in a website,
                 * and we need to be able to scan each folder in one request.
                 */
                if (is_numeric($uploadItem->getBasename()) && $uploadItem->getBasename() > 1970 && $uploadItem->getBasename() < 2100) {
                    /** @var \SplFileInfo $uploadMonth */
                    foreach (new \DirectoryIterator($uploadItem->getPathname()) as $uploadMonth) {
                        // Early bail: We don't touch links
                        if ($uploadMonth->isLink() || $this->isDot($uploadMonth)) {
                            continue;
                        }

                        if ($uploadMonth->isFile()) {
                            $this->enqueueFileInExport($uploadMonth);
                        }

                        if ($uploadMonth->isDir()) {
                            $this->enqueueDirToBeScanned($uploadMonth);
                        }
                    }
                } else {
                    if ($uploadItem->isFile()) {
                        $this->enqueueFileInExport($uploadItem);
                    }

                    if ($uploadItem->isDir()) {
                        $this->enqueueDirToBeScanned($uploadItem);
                    }
                }
            }
        }
    }

    protected function enqueueFileInExport(\SplFileInfo $file)
    {
        $normalizedPath = $this->filesystem->normalizePath($file->getPathname(), true);
        $fileSize = $file->getSize();

        $fileExtension = $file->getExtension();

        // Lazy-built relative path
        $relativePath = str_replace($this->filesystem->normalizePath(ABSPATH, true), '', $normalizedPath);

        if (isset($this->ignoreFileExtensions[$fileExtension])) {
            // Early bail: File has an ignored extension
            $this->logger->info(sprintf(
                __('%s: Skipped file "%s." Extension "%s" is excluded by rule.', 'wp-staging'),
                static::getTaskTitle(),
                $relativePath,
                $fileExtension
            ));

            return;
        }

        if (isset($this->ignoreFileExtensionFilesBiggerThan[$fileExtension])) {
            if ($fileSize > $this->ignoreFileExtensionFilesBiggerThan[$fileExtension]) {
                // Early bail: File bigger than expected for given extension
                $this->logger->info(sprintf(
                    __('%s: Skipped file "%s" (%s). It exceeds the maximum allowed file size for files with the extension "%s" (%s).', 'wp-staging'),
                    static::getTaskTitle(),
                    $relativePath,
                    size_format($fileSize),
                    $fileExtension,
                    size_format($this->ignoreFileExtensionFilesBiggerThan[$fileExtension])
                ));

                return;
            }
        } else {
            if ($fileSize > $this->ignoreFileBiggerThan) {
                // Early bail: File is larger than max allowed size.
                $this->logger->info(sprintf(
                    __('%s: Skipped file "%s" (%s). It exceeds the maximum file size for exporting (%s).', 'wp-staging'),
                    static::getTaskTitle(),
                    $relativePath,
                    size_format($fileSize),
                    size_format($this->ignoreFileBiggerThan)
                ));

                return;
            }
        }

        $this->jobDataDto->setDiscoveredFiles($this->jobDataDto->getDiscoveredFiles() + 1);
        $this->jobDataDto->setFilesystemSize($this->jobDataDto->getFilesystemSize() + $fileSize);

        // $this->logger->debug('Enqueueing file: ' . rtrim($normalizedPath, '/'));
        $this->compressorQueue->enqueue(rtrim($relativePath, '/'));
    }

    protected function enqueueDirToBeScanned(\SplFileInfo $dir)
    {
        $normalizedPath = $this->filesystem->normalizePath($dir->getPathname(), true);

        if (in_array($normalizedPath, $this->jobDataDto->getExcludedDirectories())) {
            $relativePathForLogging = str_replace($this->filesystem->normalizePath(ABSPATH, true), '', $normalizedPath);

            // Early bail: Directory is ignored
            $this->logger->info(sprintf(
                __('%s: Skipped directory "%s". Excluded by rule', 'wp-staging'),
                static::getTaskTitle(),
                $relativePathForLogging
            ));

            return;
        }

        $this->jobDataDto->setTotalDirectories($this->jobDataDto->getTotalDirectories() + 1);

        // $this->logger->debug("Enqueueing directory: $normalizedPath");
        $this->taskQueue->enqueue($normalizedPath);
    }

    protected function isDot(\SplFileInfo $fileInfo)
    {
        return $fileInfo->getBasename() === '.' || $fileInfo->getBasename() === '..';
    }

    protected function recursivePathScanning($path)
    {
        $iterator = (new FilterableDirectoryIterator())
            ->setDirectory(trailingslashit($path))
            ->setRecursive(false)
            ->setDotSkip()
            ->setWpRootPath(ABSPATH)
            ->get();

        foreach ($iterator as $item) {
            // Always check link first otherwise it may be treated as directory
            if ($item->isLink()) {
                continue;
            }

            if ($item->isDir()) {
                $this->recursivePathScanning($item->getPathname());
                continue;
            }

            if ($item->isFile()) {
                $this->enqueueFileInExport($item);
            }
        }
    }

    protected function unlockQueue()
    {
        $this->compressorQueue->shutdown();
    }
}
