<?php

namespace Pagekit\Package\Downloader;

use Pagekit\Package\Exception\DownloadErrorException;
use Pagekit\Package\PackageInterface;

interface DownloaderInterface
{
    /**
     * Downloads specific package into specific folder.
     *
     * @param  PackageInterface $package
     * @param  string           $path
     * @throws DownloadErrorException
     */
    public function download(PackageInterface $package, $path);
}
