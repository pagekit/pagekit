<?php

namespace Pagekit\Package\Repository;

use Pagekit\Package\PackageInterface;

interface RepositoryInterface extends \Countable
{
    /**
     * Checks if specified package registered.
     *
     * @param  PackageInterface $package
     * @return bool
     */
    public function hasPackage(PackageInterface $package);

    /**
     * Searches for the first match of a package by name and version.
     *
     * @param  string $name
     * @param  string $version
     * @return PackageInterface|null
     */
    public function findPackage($name, $version = 'latest');

    /**
     * Searches for all packages matching a name and optionally a version.
     *
     * @param  string $name
     * @param  string $version
     * @return PackageInterface[]
     */
    public function findPackages($name, $version = null);

    /**
     * Filters all the packages through a callback.
     *
     * @param  callable $callback
     * @param  string   $class
     * @return bool
     */
    public function filterPackages(callable $callback, $class = 'Pagekit\Package\Package');

    /**
     * Returns list of registered packages.
     *
     * @return PackageInterface[]
     */
    public function getPackages();

    /**
     * Adds package to the repository.
     *
     * @param PackageInterface $package
     */
    public function addPackage(PackageInterface $package);

    /**
     * Removes package from the repository.
     *
     * @param PackageInterface $package
     */
    public function removePackage(PackageInterface $package);


}
