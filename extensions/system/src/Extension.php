<?php

namespace Pagekit\System;

use Pagekit\Application as App;
use Pagekit\Module\Module;

class Extension extends Module
{
    /**
     * @var array
     */
    protected $parameters = [];

    /**
     * Loads the extension.
     */
    public function load(App $app, array $config)
    {
        if ($menu = $this->getConfig('menu')) {

            $app->on('system.admin_menu', function ($event) use ($menu) {
                $event->register($menu);
            });
        }

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
     * Returns the extension's parameters.
     *
     * @param  mixed $key
     * @param  mixed $default
     * @return array
     */
    public function getParams($key = null, $default = null)
    {
        if (null === $key) {
            return $this->parameters;
        }

        $array = $this->parameters;

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

    /**
     * Extension's enable hook
     */
    public function enable()
    {
    }

    /**
     * Extension's disable hook
     */
    public function disable()
    {
    }

    /**
     * Extension's uninstall hook
     */
    public function uninstall()
    {
    }
}
