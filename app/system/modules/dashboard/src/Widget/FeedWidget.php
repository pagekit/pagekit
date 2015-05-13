<?php

namespace Pagekit\Dashboard\Widget;

use Pagekit\Application as App;
use Pagekit\Widget\Model\Type;
use Pagekit\Widget\Model\WidgetInterface;

class FeedWidget extends Type
{
    public function __construct()
    {
        parent::__construct('dashboard.feed', __('Feed'), null, [
            'count' => 5,
            'content' => ''
        ]);
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
        return App::view('system/dashboard:views/feed/index.php', compact('widget', 'options'));
    }
}
