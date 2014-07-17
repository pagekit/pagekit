<?php

namespace Pagekit\System\Dashboard;

use Pagekit\Widget\Model\Type;
use Pagekit\Widget\Model\WidgetInterface;

class WeatherWidget extends Type
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
        return $this['view']->render('extension://system/views/admin/dashboard/weather/index.razr', compact('widget', 'options'));
    }

    /**
     * {@inheritdoc}
     */
    public function renderForm(WidgetInterface $widget)
    {
        return $this['view']->render('extension://system/views/admin/dashboard/weather/edit.razr', compact('widget'));
    }
}