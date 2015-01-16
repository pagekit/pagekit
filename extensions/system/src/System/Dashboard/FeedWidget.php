<?php

namespace Pagekit\System\Dashboard;

use Pagekit\Application as App;
use Pagekit\Widget\Model\TypeInterface;
use Pagekit\Widget\Model\WidgetInterface;

class FeedWidget implements TypeInterface
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
    public function getDescription(WidgetInterface $widget = null)
    {
        if (null === $widget) {
            return __('Feed Widget');
        }

        return $widget->get('title', __('No title given.'));
    }

    /**
     * {@inheritdoc}
     */
    public function render(WidgetInterface $widget, $options = [])
    {
        return App::view('extensions/system/views/admin/dashboard/feed/index.razr', compact('widget', 'options'));
    }

    /**
     * {@inheritdoc}
     */
    public function renderForm(WidgetInterface $widget)
    {
        return App::view('extensions/system/views/admin/dashboard/feed/edit.razr', compact('widget'));
    }
}
