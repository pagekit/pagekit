<?php

namespace Pagekit\Editor\Templating;

use Pagekit\Editor\Event\EditorLoadEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Templating\Helper\Helper;

class EditorHelper extends Helper
{
    /**
     * @var EventDispatcherInterface
     */
    protected $events;

    public function __construct(EventDispatcherInterface $events)
    {
        $this->events = $events;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'editor';
    }

    /**
     * Renders an editor.
     *
     * @param  string $name
     * @param  string $value
     * @param  array  $attributes
     * @param  array  $parameters
     * @return string
     */
    public function render($name, $value, array $attributes = [], $parameters = [])
    {
        if ($editor = $this->events->dispatch('editor.load', new EditorLoadEvent)->getEditor()) {
            return $editor->render($value, array_merge($attributes, compact('name')));
        }

        return __('Editor not found.');
    }
}
