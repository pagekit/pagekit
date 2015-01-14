<?php

namespace Pagekit\System\Package;

use Pagekit\Component\Package\Installer\InstallerInterface;
use Pagekit\Component\Package\Repository\InstalledRepository;

abstract class PackageManager implements \IteratorAggregate
{
    /**
     * @var InstalledRepository
     */
    protected $repository;

    /**
     * @var InstallerInterface
     */
    protected $installer;

    /**
     * @var array
     */
    protected $loaded = [];

    /**
     * Constructor.
     *
     * @param InstalledRepository $repository
     * @param InstallerInterface  $installer
     */
    public function __construct(InstalledRepository $repository, InstallerInterface $installer)
    {
        $this->repository = $repository;
        $this->installer  = $installer;
    }

    /**
     * Gets an instance by name.
     *
     * @param  string $name
     * @return mixed|null
     */
    public function get($name)
    {
        return isset($this->loaded[$name]) ? $this->loaded[$name] : null;
    }

    /**
     * Gets a repository instance.
     *
     * @return InstalledRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * Gets a installer instance.
     *
     * @return InstallerInterface
     */
    public function getInstaller()
    {
        return $this->installer;
    }

    /**
     * Implements the \IteratorAggregate.
     *
     * @return \Iterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->loaded);
    }

    /**
     * Loads an package bootstrap file.
     *
     * @param  string $name
     * @param  string $path
     * @return mixed
     */
    abstract public function load($name, $path = null);
}
