<?php

namespace Pagekit\Package\Repository;

use Pagekit\Package\PackageInterface;

class ArrayRepository implements RepositoryInterface
{
    /**
     * @var PackageInterface[]
     */
    protected $packages;

    /**
     * Constructor.
     */
    public function __construct(array $packages = [])
    {
        foreach ($packages as $package) {
            $this->addPackage($package);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function findPackage($name, $version = 'latest')
    {
        // normalize name
        $name = strtolower($name);

        if ($version == 'latest') {
            $packages = $this->findPackages($name);
            usort($packages, function($a, $b) { return version_compare($a->getVersion(), $b->getVersion()); });
            return end($packages);
        } else {
            return current($this->findPackages($name, $version));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function findPackages($name, $version = null)
    {
        // normalize name
        $name = strtolower($name);

        $packages = [];

        foreach ($this->getPackages() as $package) {
            if ($package->getName() === $name && (null === $version || $version === $package->getVersion())) {
                $packages[] = $package;
            }
        }

        return $packages;
    }

    /**
     * {@inheritdoc}
     */
    public function hasPackage(PackageInterface $package)
    {
        $packageId = $package->getUniqueName();

        foreach ($this->getPackages() as $repoPackage) {
            if ($packageId === $repoPackage->getUniqueName()) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function addPackage(PackageInterface $package)
    {
        if (null === $this->packages) {
            $this->initialize();
        }

        $package->setRepository($this);

        $this->packages[] = $package;
    }

    /**
     * {@inheritdoc}
     */
    public function filterPackages(callable $callback, $class = 'Pagekit\Package\Package')
    {
        foreach ($this->getPackages() as $package) {
            if (false === call_user_func($callback, $package)) {
                return false;
            }
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function removePackage(PackageInterface $package)
    {
        $packageId = $package->getUniqueName();

        foreach ($this->getPackages() as $key => $repoPackage) {
            if ($packageId === $repoPackage->getUniqueName()) {
                array_splice($this->packages, $key, 1);

                return;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getPackages()
    {
        if (null === $this->packages) {
            $this->initialize();
        }

        return $this->packages;
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->getPackages());
    }

    /**
     * Initializes the packages array. Mostly meant as an extension point.
     */
    protected function initialize()
    {
        $this->packages = [];
    }
}
