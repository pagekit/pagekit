<?php

namespace Pagekit\Package\Repository;

use Pagekit\Package\PackageInterface;

interface InstalledRepositoryInterface extends RepositoryInterface
{
    /**
     * Checks if specified package registered.
     *
     * @param  PackageInterface $package
     * @return string
     */
    public function getInstallPath(PackageInterface $package);
}
