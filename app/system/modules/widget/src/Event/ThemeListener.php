<?php

namespace Pagekit\Widget\Event;

use Pagekit\System\Theme;
use Pagekit\Event\EventSubscriberInterface;

class ThemeListener implements EventSubscriberInterface
{
    /**
     * @var Theme
     */
    protected $theme;

    /**
     * Constructor.
     *
     * @param Theme $theme
     */
    public function __construct(Theme $theme)
    {
        $this->theme = $theme;
    }

    /**
     * Sets the widget theme config.
     *
     * @param $event
     * @param $widget
     */
    public function onInit($event, $widget)
    {
        $config  = $this->theme->config("widgets.".$widget->getId(), []);
        $default = $this->theme->config("widget", []);

        $widget->theme = array_replace($default, $config);
    }

    /**
     * Saves the widget theme config.
     *
     * @param $event
     * @param $widget
     */
    public function onSaved($event, $widget)
    {
        if (!isset($widget->theme)) {
            return;
        }

        $this->theme->config['widgets'][$widget->getId()] = $widget->theme;
        $this->theme->save();
    }

    /**
     * {@inheritdoc}
     */
    public function subscribe()
    {
        return [
            'model.widget.init' => 'onInit',
            'model.widget.saved' => 'onSaved'
        ];
    }
}
