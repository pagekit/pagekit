<?php

namespace Pagekit\Dashboard;

use Pagekit\Application as App;
use Pagekit\Widget\Model\TypeInterface;
use Pagekit\Widget\Model\WidgetInterface;

class WeatherWidget implements TypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'widget.weather';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return __('Weather');
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
        return App::view('extensions/system/modules/dashboard/views/weather/index.razr', compact('widget', 'options'));
    }

    /**
     * {@inheritdoc}
     */
    public function renderForm(WidgetInterface $widget)
    {
        return App::view('extensions/system/modules/dashboard/views/weather/edit.razr', compact('widget'));
    }
}
