<?php

namespace Pagekit\View\Helper;

use Pagekit\Config\Config;

class ConfigHelper extends Helper
{
    /**
     * @var Config
     */
    protected $config;

    public function __construct()
    {
        $this->config = new Config();
    }

    /**
     * Get shortcut.
     *
     * @see get()
     */
    public function __invoke($key, $default = false)
    {
        return $this->get($key, $default);
    }

    /**
     * Adds config values.
     *
     * @param  mixed $values
     * @param  bool  $replace
     * @return self
     */
    public function add(array $values, $replace = false)
    {
        $this->config->merge($values, $replace);

        return $this;
    }

    /**
     * Gets a value by key.
     *
     * @param  string $key
     * @param  mixed  $default
     * @return mixed
     */
    public function get($key, $default)
    {
        return $this->config->get($key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'config';
    }
}
