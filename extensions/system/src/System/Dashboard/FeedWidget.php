<?php

namespace Pagekit\System\Dashboard;

use Pagekit\Widget\Model\Type;
use Pagekit\Widget\Model\WidgetInterface;

class FeedWidget extends Type
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'widget.feed';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return __('Feed');
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return __('Feed Widget');
    }

    /**
     * {@inheritdoc}
     */
    public function getInfo(WidgetInterface $widget)
    {
        $settings = $widget->getSettings();

        return isset($settings['title']) ? $settings['title'] : '';
    }

    /**
     * {@inheritdoc}
     */
    public function render(WidgetInterface $widget, $options = array())
    {
        return $this('view')->render('system/admin/dashboard/feed/index.razr.php', compact('widget', 'options'));
    }

    /**
     * {@inheritdoc}
     */
    public function renderForm(WidgetInterface $widget)
    {
        return $this('view')->render('system/admin/dashboard/feed/edit.razr.php', compact('widget'));
    }
}