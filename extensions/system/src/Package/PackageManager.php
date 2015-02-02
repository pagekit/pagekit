<?php

namespace Pagekit\System\Package;

use Pagekit\Package\Installer\InstallerInterface;
use Pagekit\Package\Installer\PackageInstaller;
use Pagekit\Package\Repository\InstalledRepository;

class PackageManager
{
    /**
     * @var array
     */
    protected $repositories = [];

    /**
     * Gets a installer instance.
     *
     * @param  string $name
     * @return InstallerInterface
     */
    public function getInstaller($name)
    {
        if (!$repository = $this->getRepository($name)) {
            throw new \InvalidArgumentException("Invalid repository '$name'");
        }

        return new PackageInstaller($repository);
    }

    /**
     * Gets a repository instance.
     *
     * @param  string $name
     * @return InstalledRepository
     */
    public function getRepository($name)
    {
        return isset($this->repositories[$name]) ? $this->repositories[$name] : null;
    }

    /**
     * Adds a repository instance.
     *
     * @param string              $name
     * @param InstalledRepository $repository
     */
    public function addRepository($name, InstalledRepository $repository)
    {
        $this->repositories[$name] = $repository;
    }
}
