<?php

namespace Pagekit\Package\Installer;

use Pagekit\Package\Exception\LogicException;
use Pagekit\Package\PackageInterface;

interface InstallerInterface
{
    /**
     * Installs specific package.
     *
     * @param  string  $packageFile
     * @throws LogicException
     */
    public function install($packageFile);

    /**
     * Updates specific package.
     *
     * @param  string  $packageFile
     * @throws LogicException
     */
    public function update($packageFile);

    /**
     * Uninstalls specific package.
     *
     * @param  PackageInterface $package
     * @throws LogicException
     */
    public function uninstall(PackageInterface $package);

    /**
     * Checks that provided package is installed.
     *
     * @param  PackageInterface $package
     * @return bool
     */
    public function isInstalled(PackageInterface $package);
}
