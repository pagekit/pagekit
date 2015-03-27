<?php

namespace Pagekit\Package\Repository;

use Pagekit\Package\PackageInterface;

class InstalledRepository extends ArrayRepository implements InstalledRepositoryInterface
{
    /**
     * @var string
     */
    protected $path;

    /**
     * Constructor.
     *
     * @param string $path
     */
    public function __construct($path)
    {
        parent::__construct();

        $this->path = rtrim($path, '\/');
    }

    /**
     * Get the repository path.
     *
     * return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * {@inheritdoc}
     */
    public function getInstallPath(PackageInterface $package)
    {
        return $this->path.'/'.$package->getName();
    }
}
