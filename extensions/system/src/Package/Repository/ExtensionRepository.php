<?php

namespace Pagekit\Package\Repository;

use Pagekit\Package\Loader\JsonLoader;
use Pagekit\Package\Loader\LoaderInterface;

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
    public function __construct($path, LoaderInterface $loader = null)
    {
        parent::__construct($path);

        $this->loader = $loader ?: new JsonLoader();
    }

    /**
     * {@inheritdoc}
     */
    protected function initialize()
    {
        parent::initialize();

        if (empty($this->packages)) {
            foreach (glob("{$this->path}/*/extension.json") as $config) {
                $this->addPackage($this->loader->load($config));
            }
        }
    }
}
