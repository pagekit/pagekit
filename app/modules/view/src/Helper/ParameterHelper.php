<?php

namespace Pagekit\View\Helper;

use Pagekit\View\Event\ViewEvent;
use Pagekit\View\View;

class ParameterHelper implements HelperInterface
{
    /**
     * @var ViewEvent
     */
    protected $event;

    /**
     * {@inheritdoc}
     */
    public function register(View $view)
    {
        $view->on('render', function ($event) {
            $this->event = $event;
        });
    }

    /**
     * Get shortcut.
     *
     * @see get()
     */
    public function __invoke($key, $default = false)
    {
        return $this->get($key, $default);
    }

    /**
     * Gets a value by key.
     *
     * @param  string $key
     * @param  mixed  $default
     * @return mixed
     */
    public function get($key, $default)
    {
        return $this->event->get($key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'param';
    }
}
