<?php

namespace Pagekit\Widget\Event;

use Pagekit\System\Theme;
use Pagekit\Event\EventSubscriberInterface;

class ThemeListener implements EventSubscriberInterface
{
    /**
     * @var \Pagekit\System\Theme
     */
    protected $theme;

    /**
     * Constructor.
     *
     * @param \Pagekit\System\Theme $theme
     */
    public function __construct(Theme $theme)
    {
        $this->theme = $theme;
    }

    /**
     * Sets the widget theme data.
     *
     * @param \Pagekit\Event\Event $event
     * @param \Pagekit\Widget\Model\Widget $widget
     */
    public function onWidgetInit($event, $widget)
    {
        $config  = $this->theme->get("data.widgets.".$widget->id, []);
        $default = $this->theme->config("widget", []);

        $widget->theme = array_replace($default, $config);
    }

    /**
     * Saves the widget theme data.
     *
     * @param \Pagekit\Event\Event $event
     * @param \Pagekit\Widget\Model\Widget $widget
     * @param array $data
     */
    public function onWidgetSaved($event, $widget, $data)
    {
        if (!isset($data['theme'])) {
            return;
        }

        $this->theme->options['data']['widgets'][$widget->id] = $data['theme'];
        $this->theme->save();
    }

    /**
     * {@inheritdoc}
     */
    public function subscribe()
    {
        return [
            'model.widget.init' => 'onWidgetInit',
            'model.widget.saved' => 'onWidgetSaved'
        ];
    }
}
