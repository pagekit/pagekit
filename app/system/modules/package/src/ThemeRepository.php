<?php

namespace Pagekit\System;

use Pagekit\Package\Loader\JsonLoader;
use Pagekit\Package\Loader\LoaderInterface;
use Pagekit\Package\Repository\InstalledRepository;

class ThemeRepository extends InstalledRepository
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

        foreach (glob("{$this->path}/*/theme.json") as $config) {
            $this->addPackage($this->loader->load($config));
        }
    }
}
