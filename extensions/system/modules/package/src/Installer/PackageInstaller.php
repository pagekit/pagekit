<?php

namespace Pagekit\Package\Installer;

use Pagekit\Filesystem\Filesystem;
use Pagekit\Package\Exception\LogicException;
use Pagekit\Package\Loader\JsonLoader;
use Pagekit\Package\Loader\LoaderInterface;
use Pagekit\Package\PackageInterface;
use Pagekit\Package\Repository\InstalledRepositoryInterface;

/**
 * Package installation manager.
 */
class PackageInstaller implements InstallerInterface
{
    /**
     * @var InstalledRepositoryInterface
     */
    protected $repository;

    /**
     * @var LoaderInterface
     */
    protected $loader;

    /**
     * @var Filesystem
     */
    protected $file;

    /**
     * Initializes the installer.
     *
     * @param InstalledRepositoryInterface $repository
     * @param LoaderInterface              $loader
     * @param Filesystem                   $file
     */
    public function __construct(InstalledRepositoryInterface $repository, LoaderInterface $loader = null, Filesystem $file = null)
    {
        $this->repository = $repository;
        $this->loader     = $loader ?: new JsonLoader;
        $this->file       = $file ?: new Filesystem;
    }

    /**
     * {@inheritdoc}
     */
    public function install($packageFile)
    {
        $package = $this->loader->load($packageFile);

        if ($this->repository->hasPackage($package)) {
            throw new LogicException('Package is already installed: ' . $package);
        }

        $this->file->copyDir(dirname($packageFile), $this->repository->getInstallPath($package));
        $this->repository->addPackage(clone $package);
    }

    /**
     * {@inheritdoc}
     */
    public function update($packageFile)
    {
        $update = $this->loader->load($packageFile);

        if (!$initial = $this->repository->findPackage($update->getName())) {
            throw new LogicException('Package is not installed: ' . $initial);
        }

        $installPath = $this->repository->getInstallPath($initial);

        $this->file->delete($installPath);
        $this->repository->removePackage($initial);
        $this->file->copyDir(dirname($packageFile), $installPath);

        if (!$this->repository->hasPackage($update)) {
            $this->repository->addPackage(clone $update);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function uninstall(PackageInterface $package)
    {
        if (!$this->repository->hasPackage($package)) {
            throw new LogicException('Package is not installed: ' . $package);
        }

        $this->file->delete($this->repository->getInstallPath($package));
        $this->repository->removePackage($package);
    }

    /**
     * {@inheritdoc}
     */
    public function isInstalled(PackageInterface $package)
    {
        return is_dir($this->repository->getInstallPath($package));
    }
}
