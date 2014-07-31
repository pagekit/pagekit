<?php

namespace Pagekit\Extension\Package;

use Pagekit\Component\Package\Loader\LoaderInterface;
use Pagekit\Component\Package\Repository\InstalledRepository;

class ExtensionRepository extends InstalledRepository
{
    /**
     * @var LoaderInterface
     */
    protected $loader;

    /**
     * Constructor.
     *
     * @param string          $path
     * @param LoaderInterface $loader
     */
    public function __construct($path, LoaderInterface $loader)
    {
        parent::__construct($path);

        $this->loader = $loader;
    }

    /**
     * {@inheritdoc}
     */
    protected function initialize()
    {
        parent::initialize();

        if (empty($this->packages)) {
            $packages = array_merge(
                glob("{$this->path}/*/extension.json"),
                glob("{$this->path}/*/*/extension.json")
            );
            foreach ($packages as $config) {
                $this->addPackage($this->loader->load($config));
            }
        }
    }
}
