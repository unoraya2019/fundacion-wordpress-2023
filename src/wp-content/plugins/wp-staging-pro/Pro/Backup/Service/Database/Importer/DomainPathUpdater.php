<?php

namespace WPStaging\Pro\Backup\Service\Database\Importer;

use UnexpectedValueException;
use WPStaging\Pro\Backup\Dto\Job\JobImportDataDto;

class DomainPathUpdater
{
    protected $sites;

    private $sourceSiteDomain;

    private $sourceSitePath;

    protected $isSourceSubdomainInstall;

    public function getSourceSiteDomain()
    {
        return $this->sourceSiteDomain;
    }

    public function getSourceSitePath()
    {
        return $this->sourceSitePath;
    }

    public function getIsSourceSubdomainInstall()
    {
        return $this->isSourceSubdomainInstall;
    }

    public function setSourceSiteDomain($sourceSiteDomain)
    {
        $this->sourceSiteDomain = $sourceSiteDomain;
    }

    public function setSourceSitePath($sourceSitePath)
    {
        $this->sourceSitePath = $sourceSitePath;
    }

    public function setSourceSubdomainInstall($isSubdomainInstall)
    {
        $this->isSourceSubdomainInstall = $isSubdomainInstall;
    }

    public function setSourceSites($sites)
    {
        $this->sites = $sites;
    }

    public function getSitesWithNewURLs($baseDomain, $basePath, $homeURL, $isSubdomainInstall)
    {
        $adjustedSites = [];
        foreach ($this->sites as $site) {
            $adjustedSites[] = $this->adjustSiteDomainPath($site, $baseDomain, $basePath, $homeURL, $isSubdomainInstall);
        }

        return $adjustedSites;
    }

    public function readMetaData(JobImportDataDto $jobDataDto)
    {
        $this->isSourceSubdomainInstall = $jobDataDto->getBackupMetadata()->getSubdomainInstall();

        $sourceSiteURL = $jobDataDto->getBackupMetadata()->getSiteUrl();
        $sourceSiteURLWithoutWWW = str_ireplace('//www.', '//', $sourceSiteURL);
        $parsedURL = parse_url($sourceSiteURLWithoutWWW);

        if (!is_array($parsedURL) || !array_key_exists('host', $parsedURL)) {
            throw new UnexpectedValueException("Bad URL format, cannot proceed.");
        }

        $this->sourceSiteDomain = $parsedURL['host'];
        $this->sourceSitePath = '/';
        if (array_key_exists('path', $parsedURL)) {
            $this->sourceSitePath = $parsedURL['path'];
        }

        $this->sites = $jobDataDto->getBackupMetadata()->getSites();
    }

    private function adjustSiteDomainPath($site, $baseDomain, $basePath, $homeURL, $isSubdomainInstall)
    {
        $subsiteDomain = str_replace($this->sourceSiteDomain, $baseDomain, $site['domain']);
        $subsitePath = str_replace(trailingslashit($this->sourceSitePath), $basePath, $site['path']);
        $subsiteUrlWithoutScheme = untrailingslashit($subsiteDomain . $subsitePath);
        $mainsiteUrlWithoutScheme = untrailingslashit($baseDomain . $basePath);

        $wwwPrefix = '';
        if (strpos($homeURL, '//www.') !== false) {
            $wwwPrefix = 'www.';
        }

        if ($this->isSourceSubdomainInstall === $isSubdomainInstall || $subsiteUrlWithoutScheme === $mainsiteUrlWithoutScheme) {
            $site['new_url'] = parse_url($homeURL, PHP_URL_SCHEME) . '://' . $wwwPrefix . $subsiteUrlWithoutScheme;
            $site['new_domain'] = $subsiteDomain;
            $site['new_path'] = $subsitePath;
            return $site;
        }

        $subsiteDomain = $baseDomain;
        $subsitePath = $basePath;

        $subsiteName = str_replace($mainsiteUrlWithoutScheme, '', $subsiteUrlWithoutScheme);
        $subsiteName = rtrim($subsiteName, '.');
        $subsiteName = trim($subsiteName, '/');
        if ($isSubdomainInstall) {
            $subsiteDomain = $subsiteName . '.' . $subsiteDomain;
        }

        if (!$isSubdomainInstall) {
            $subsitePath = $subsitePath . trailingslashit($subsiteName);
        }

        $subsiteUrlWithoutScheme = trailingslashit(rtrim($subsiteDomain) . $subsitePath);
        $site['new_url'] = parse_url($homeURL, PHP_URL_SCHEME) . '://' . $wwwPrefix . $subsiteUrlWithoutScheme;
        $site['new_domain'] = $subsiteDomain;
        $site['new_path'] = $subsitePath;
        return $site;
    }
}
