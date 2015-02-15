<?php

namespace Pagekit\System\Event;

use Pagekit\Application;
use Pagekit\Config\Config;
use Symfony\Component\EventDispatcher\Event;

class SettingsEvent extends Event
{
    /**
     * @var array
     */
    protected $settings = [];

    /**
     * @var Config
     */
    protected $config;

    /**
     * Constructor.
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return array
     */
    public function get()
    {
        return $this->settings;
    }

    /**
     * Add settings view.
     *
     * @param string $name
     * @param string $label
     * @param string|callable $settings
     */
    public function add($name, $label, $settings)
    {
        if (is_callable($settings)) {
            $settings = call_user_func_array($settings, [$this->config]);
        }

        $this->settings[$name] = ['label' => $label, 'view' => $settings];
    }
}
