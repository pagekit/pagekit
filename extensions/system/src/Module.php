<?php

namespace Pagekit\System;

use Pagekit\Application as App;

class Module
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var array
     */
    protected $parameters = [];

    /**
     * Constructor.
     */
    public function __construct(App $app, array $config)
    {
        $this->config = $config;

        if ($this->getConfig('parameters.settings')) {

            if (is_array($defaults = $this->getConfig('parameters.settings.defaults'))) {
                $this->parameters = array_replace($this->parameters, $defaults);
            }

            if (is_array($settings = App::option("{$config['name']}:settings"))) {
                $this->parameters = array_replace($this->parameters, $settings);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->config['name'];
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return $this->config['path'];
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig($key = null, $default = null)
    {
        return $this->fetch($this->config, $key, $default);
    }

    /**
     * Returns the extension's parameters.
     *
     * @param  mixed $key
     * @param  mixed $default
     * @return array
     */
    public function getParams($key = null, $default = null)
    {
        return $this->fetch($this->parameters, $key, $default);
    }

    protected function fetch($array, $key = null, $default = null)
    {
        if (null === $key) {
            return $array;
        }

        if (isset($array[$key])) {
            return $array[$key];
        }

        foreach (explode('.', $key) as $segment) {

            if (!is_array($array) || !array_key_exists($segment, $array)) {
                return $default;
            }

            $array = $array[$segment];
        }

        return $array;
    }
}
