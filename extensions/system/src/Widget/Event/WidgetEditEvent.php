<?php

namespace Pagekit\Widget\Event;

class WidgetEditEvent extends WidgetEvent
{
    /**
     * @var array
     */
    protected $settings = [];

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
     * @param string   $name
     * @param string|callable $settings
     */
    public function addSettings($name, $settings)
    {
        if (is_callable($settings)) {
            $settings = call_user_func_array($settings, [$this->getWidget()]);
        }

        $this->settings[$name] = $settings;
    }
}
