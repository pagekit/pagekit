<?php

namespace Pagekit\System\Event;

use Pagekit\Application;
use Symfony\Component\EventDispatcher\Event;

class SettingsEvent extends Event
{
    /**
     * @var array
     */
    protected $settings = [];

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
            $settings = call_user_func_array($settings, [Application::getInstance()]);
        }

        $this->settings[$name] = ['label' => $label, 'view' => $settings];
    }
}
