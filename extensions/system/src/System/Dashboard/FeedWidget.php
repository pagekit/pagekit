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