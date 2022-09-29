<?php

namespace WPStaging\Pro\Backup\Service\Database\Importer;

use WPStaging\Framework\Database\SearchReplace;
use WPStaging\Pro\Backup\Dto\Job\JobImportDataDto;

class DatabaseSearchReplacer
{
    protected $search  = [];

    protected $replace = [];

    protected $sourceSiteUrl;

    protected $sourceHomeUrl;

    protected $sourceSiteHostname;

    protected $sourceHomeHostname;

    protected $destinationSiteUrl;

    protected $destinationHomeUrl;

    protected $destinationSiteHostname;

    protected $destinationHomeHostname;

    protected $matchingScheme;

    protected $plugins = [];

    public function getSearchAndReplace(JobImportDataDto $jobDataDto, $destinationSiteUrl, $destinationHomeUrl, $absPath = ABSPATH)
    {
        $this->plugins = $jobDataDto->getBackupMetadata()->getPlugins();

        $this->sourceSiteUrl = untrailingslashit($jobDataDto->getBackupMetadata()->getSiteUrl());
        $this->sourceHomeUrl = untrailingslashit($jobDataDto->getBackupMetadata()->getHomeUrl());

        $this->sourceSiteHostname = untrailingslashit($this->buildHostname($this->sourceSiteUrl));
        $this->sourceHomeHostname = untrailingslashit($this->buildHostname($this->sourceHomeUrl));

        $this->destinationSiteUrl = untrailingslashit($destinationSiteUrl);
        $this->destinationHomeUrl = untrailingslashit($destinationHomeUrl);

        $this->destinationSiteHostname = untrailingslashit($this->buildHostname($this->destinationSiteUrl));
        $this->destinationHomeHostname = untrailingslashit($this->buildHostname($this->destinationHomeUrl));

        $this->matchingScheme = parse_url($this->sourceSiteUrl, PHP_URL_SCHEME) === parse_url($this->destinationSiteUrl, PHP_URL_SCHEME);

        if ($this->matchingScheme) {
            $this->replaceGenericScheme();
        } else {
            $this->replaceMultipleSchemes();
            $this->replaceGenericScheme();
        }

                array_push(
                    $this->search,
                    $jobDataDto->getBackupMetadata()->getAbsPath(),
                    addcslashes($jobDataDto->getBackupMetadata()->getAbsPath(), '/'),
                    urlencode($jobDataDto->getBackupMetadata()->getAbsPath())
                );

        array_push(
            $this->replace,
            $absPath,
            addcslashes($absPath, '/'),
            urlencode($absPath)
        );

        if (urlencode($jobDataDto->getBackupMetadata()->getAbsPath()) !== rawurlencode($jobDataDto->getBackupMetadata()->getAbsPath())) {
            array_push(
                $this->search,
                rawurlencode($jobDataDto->getBackupMetadata()->getAbsPath())
            );
            array_push(
                $this->replace,
                rawurlencode($absPath)
            );
        }

        if (wp_normalize_path($jobDataDto->getBackupMetadata()->getAbsPath()) !== $jobDataDto->getBackupMetadata()->getAbsPath()) {
            array_push(
                $this->search,
                wp_normalize_path($jobDataDto->getBackupMetadata()->getAbsPath()),
                wp_normalize_path(addcslashes($jobDataDto->getBackupMetadata()->getAbsPath(), '/')),
                wp_normalize_path(urlencode($jobDataDto->getBackupMetadata()->getAbsPath()))
            );

            array_push(
                $this->replace,
                wp_normalize_path($absPath),
                wp_normalize_path(addcslashes($absPath, '/')),
                wp_normalize_path(urlencode($absPath))
            );

            if (
                wp_normalize_path(urlencode($jobDataDto->getBackupMetadata()->getAbsPath())) !==
                wp_normalize_path(rawurlencode($jobDataDto->getBackupMetadata()->getAbsPath()))
            ) {
                array_push(
                    $this->search,
                    wp_normalize_path(rawurlencode($jobDataDto->getBackupMetadata()->getAbsPath()))
                );
                array_push(
                    $this->replace,
                    wp_normalize_path(rawurlencode($absPath))
                );
            }
        }

        foreach ($this->search as $k => $searchItem) {
            if ($this->replace[$k] === $searchItem) {
                unset($this->search[$k]);
                unset($this->replace[$k]);
            }
        }

                $this->search = array_values($this->search);
        $this->replace = array_values($this->replace);

                $searchReplaceToSort = array_combine($this->search, $this->replace);

        uksort($searchReplaceToSort, function ($item1, $item2) {
            if (strlen($item1) == strlen($item2)) {
                return 0;
            }
            return (strlen($item1) > strlen($item2)) ? -1 : 1;
        });

        $orderedSearch = array_keys($searchReplaceToSort);
        $orderedReplace = array_values($searchReplaceToSort);

        return (new SearchReplace())
            ->setSearch($orderedSearch)
            ->setReplace($orderedReplace)
            ->setWpBakeryActive($jobDataDto->getBackupMetadata()->getWpBakeryActive());
    }

    public function buildHostname($url)
    {
        $parsedUrl = parse_url($url);

        if (!is_array($parsedUrl) || !array_key_exists('host', $parsedUrl)) {
            throw new \UnexpectedValueException("Bad URL format, cannot proceed.");
        }

                $hostname = $parsedUrl['host'];

        if (array_key_exists('path', $parsedUrl)) {
            $hostname = trailingslashit($hostname) . trim($parsedUrl['path'], '/');
        }

        return $hostname;
    }

    protected function replaceGenericScheme()
    {
        $sourceSiteHostnameGenericProtocol = '//' . $this->sourceSiteHostname;
        $destinationSiteHostnameGenericProtocol = '//' . $this->destinationSiteHostname;

        $jsonEscapedSourceSiteHostnameGenericProtocol = addcslashes($sourceSiteHostnameGenericProtocol, '/');
        $jsonEscapedDestinationSiteHostnameGenericProtocol = addcslashes($destinationSiteHostnameGenericProtocol, '/');

        array_push(
            $this->search,
            $sourceSiteHostnameGenericProtocol,
            $jsonEscapedSourceSiteHostnameGenericProtocol,
            urlencode($sourceSiteHostnameGenericProtocol)
        );

        array_push(
            $this->replace,
            $destinationSiteHostnameGenericProtocol,
            $jsonEscapedDestinationSiteHostnameGenericProtocol,
            urlencode($destinationSiteHostnameGenericProtocol)
        );

        if ($this->sourceSiteHostname !== $this->sourceHomeHostname) {
            $sourceHomeHostnameGenericProtocol = '//' . $this->sourceHomeHostname;
            $destinationHomeHostnameGenericProtocol = '//' . $this->destinationHomeHostname;

            $jsonEscapedSourceHomeHostnameGenericProtocol = addcslashes($sourceHomeHostnameGenericProtocol, '/');
            $jsonEscapedDestinationHomeHostnameGenericProtocol = addcslashes($destinationHomeHostnameGenericProtocol, '/');

            array_push(
                $this->search,
                $sourceHomeHostnameGenericProtocol,
                $jsonEscapedSourceHomeHostnameGenericProtocol,
                urlencode($sourceHomeHostnameGenericProtocol)
            );

            array_push(
                $this->replace,
                $destinationHomeHostnameGenericProtocol,
                $jsonEscapedDestinationHomeHostnameGenericProtocol,
                urlencode($destinationHomeHostnameGenericProtocol)
            );
        }

        if ($this->isExtendedSearchReplaceActivated()) {
            $this->search[] = addcslashes($jsonEscapedSourceSiteHostnameGenericProtocol, '/');
            $this->replace[] = addcslashes($jsonEscapedDestinationSiteHostnameGenericProtocol, '/');

            if ($this->sourceSiteHostname !== $this->sourceHomeHostname) {
                $this->search[] = addcslashes($jsonEscapedSourceHomeHostnameGenericProtocol, '/');
                $this->replace[] = addcslashes($jsonEscapedDestinationHomeHostnameGenericProtocol, '/');
            }
        }
    }

    protected function replaceMultipleSchemes()
    {
        $jsonEscapedHttpsSourceSiteHostname = addcslashes('https://' . $this->sourceSiteHostname, '/');
        $jsonEscapedHttpSourceSiteHostname = addcslashes('http://' . $this->sourceSiteHostname, '/');

        array_push(
            $this->search,
            'https://' . $this->sourceSiteHostname,
            'http://' . $this->sourceSiteHostname,
            $jsonEscapedHttpsSourceSiteHostname,
            $jsonEscapedHttpSourceSiteHostname,
            urlencode('https://' . $this->sourceSiteHostname),
            urlencode('http://' . $this->sourceSiteHostname)
        );

        array_push(
            $this->replace,
            $this->destinationSiteUrl,
            $this->destinationSiteUrl,
            addcslashes($this->destinationSiteUrl, '/'),
            addcslashes($this->destinationSiteUrl, '/'),
            urlencode($this->destinationSiteUrl),
            urlencode($this->destinationSiteUrl)
        );

        if ($this->sourceSiteHostname !== $this->sourceHomeHostname) {
            $jsonEscapedHttpsSourceHomeHostname = addcslashes('https://' . $this->sourceHomeHostname, '/');
            $jsonEscapedHttpSourceHomeHostname = addcslashes('http://' . $this->sourceHomeHostname, '/');

            array_push(
                $this->search,
                'https://' . $this->sourceHomeHostname,
                'http://' . $this->sourceHomeHostname,
                $jsonEscapedHttpsSourceHomeHostname,
                $jsonEscapedHttpSourceHomeHostname,
                urlencode('https://' . $this->sourceHomeHostname),
                urlencode('http://' . $this->sourceHomeHostname)
            );

            array_push(
                $this->replace,
                $this->destinationHomeUrl,
                $this->destinationHomeUrl,
                addcslashes($this->destinationHomeUrl, '/'),
                addcslashes($this->destinationHomeUrl, '/'),
                urlencode($this->destinationHomeUrl),
                urlencode($this->destinationHomeUrl)
            );
        }

        if ($this->isExtendedSearchReplaceActivated()) {
            $this->search[] = addcslashes($jsonEscapedHttpsSourceSiteHostname, '/');
            $this->search[] = addcslashes($jsonEscapedHttpSourceSiteHostname, '/');
            $this->replace[] = addcslashes($this->destinationSiteUrl, '/');
            $this->replace[] = addcslashes($this->destinationSiteUrl, '/');

            if ($this->sourceSiteHostname !== $this->sourceHomeHostname) {
                $this->search[] = addcslashes($jsonEscapedHttpsSourceHomeHostname, '/');
                $this->search[] = addcslashes($jsonEscapedHttpSourceHomeHostname, '/');
                $this->replace[] = addcslashes($this->destinationHomeUrl, '/');
                $this->replace[] = addcslashes($this->destinationHomeUrl, '/');
            }
        }
    }

    protected function isExtendedSearchReplaceActivated()
    {
        $pluginWhoseDataRequireExtraRulesInstalled = false;
        foreach ($this->plugins as $plugin) {
            if (in_array($plugin, $this->getPluginsWhichRequireExtraSearchReplaceRules())) {
                $pluginWhoseDataRequireExtraRulesInstalled = true;
                break;
            }
        }

        return apply_filters('wpstg.backup.restore.extended-search-replace', $pluginWhoseDataRequireExtraRulesInstalled) === true;
    }

    protected function getPluginsWhichRequireExtraSearchReplaceRules()
    {
        return [
            'revslider/revslider.php'
        ];
    }
}
