<?php

namespace Pagekit\System\Package;

use Composer\Autoload\ClassLoader;
use Pagekit\Component\File\ResourceLocator;
use Pagekit\Component\Package\Installer\InstallerInterface;
use Pagekit\Component\Package\Repository\InstalledRepository;
use Pagekit\Framework\Application;

abstract class PackageManager implements \IteratorAggregate
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var InstalledRepository
     */
    protected $repository;

    /**
     * @var InstallerInterface
     */
    protected $installer;

    /**
     * @var ClassLoader
     */
    protected $autoloader;

    /**
     * @var ResourceLocator
     */
    protected $locator;

    /**
     * @var array
     */
    protected $loaded = [];

    /**
     * Constructor.
     *
     * @param Application         $app
     * @param InstalledRepository $repository
     * @param InstallerInterface  $installer
     * @param ClassLoader         $autoloader
     * @param ResourceLocator     $locator
     */
    public function __construct(Application $app, InstalledRepository $repository, InstallerInterface $installer, ClassLoader $autoloader, ResourceLocator $locator)
    {
        $this->app        = $app;
        $this->repository = $repository;
        $this->installer  = $installer;
        $this->autoloader = $autoloader;
        $this->locator    = $locator;
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
