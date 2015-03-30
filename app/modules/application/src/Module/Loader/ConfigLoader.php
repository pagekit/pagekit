<?php

namespace Pagekit\Module\Loader;

use Pagekit\Config\Config;

class ConfigLoader implements LoaderInterface
{
    /**
     * @var Config
     */
    protected $config = [];

    /**
     * Constructor.
     *
     * @param Config $config
     */
    public function __construct(Config $config) {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function load($name, array $config)
    {
        return array_replace_recursive($config, ['config' => $this->config->get($name, [])]);
    }
}
