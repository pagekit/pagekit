<?php

namespace Pagekit\Dashboard\Widget;

use Pagekit\Application as App;
use Pagekit\Widget\Model\Type;
use Pagekit\Widget\Model\WidgetInterface;

class WeatherWidget extends Type
{
    public function __construct()
    {
        parent::__construct('widget.weather', __('Weather'));
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription(WidgetInterface $widget = null)
    {
        if (null === $widget) {
            return __('Weather Widget');
        }

        return $widget->get('location', __('No location chosen.'));
    }

    /**
     * {@inheritdoc}
     */
    public function render(WidgetInterface $widget, $options = [])
    {
        return App::view('system/dashboard:views/weather/index.php', compact('widget', 'options'));
    }

    /**
     * {@inheritdoc}
     */
    public function renderForm(WidgetInterface $widget)
    {
        return App::view('system/dashboard:views/weather/edit.php', compact('widget'));
    }
}
