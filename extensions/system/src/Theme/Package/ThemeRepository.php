<?php

namespace Pagekit\Theme\Package;

use Pagekit\Component\Package\Loader\LoaderInterface;
use Pagekit\Component\Package\Repository\InstalledRepository;

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

        foreach (glob("{$this->path}/*/theme.json") as $config) {
            $this->addPackage($this->loader->load($config));
        }
    }
}
