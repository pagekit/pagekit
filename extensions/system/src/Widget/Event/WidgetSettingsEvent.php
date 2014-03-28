<?php

namespace Pagekit\Widget\Event;

use Pagekit\Framework\Event\Event;

class WidgetSettingsEvent extends Event
{
    /**
     * @var array
     */
    protected $settings = array();

    /**
     * @param array $settings
     */
    public function setSettings($settings)
    {
        $this->settings = $settings;
    }

    /**
     * @return array
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * Add settings view.
     *
     * @param string $name
     * @param string $view
     */
    public function addSettings($name, $view)
    {
        $this->settings[$name] = $view;
    }
}
