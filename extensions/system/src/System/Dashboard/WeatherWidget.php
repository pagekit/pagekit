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
    public function getDescription()
    {
        return __('Weather Widget');
    }

    /**
     * {@inheritdoc}
     */
    public function getInfo(WidgetInterface $widget)
    {

        $settings = $widget->getSettings();

        return isset($settings['location']) ? $settings['location'] : '';
    }

    /**
     * {@inheritdoc}
     */
    public function render(WidgetInterface $widget, $options = array())
    {
        return $this('view')->render('system/admin/dashboard/weather/index.razr.php', compact('widget', 'options'));
    }

    /**
     * {@inheritdoc}
     */
    public function renderForm(WidgetInterface $widget)
    {
        return $this('view')->render('system/admin/dashboard/weather/edit.razr.php', compact('widget'));
    }
}