<?php

namespace Pagekit\Editor;

use Pagekit\Application as App;
use Pagekit\Editor\Event\EditorLoadEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class Editor implements EditorInterface, EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public function render($value, array $attributes = [])
    {
        App::view()->style('codemirror');
        App::view()->script('editor');

        $attributes = array_merge([
            'data-editor' => true, 'autocomplete' => 'off', 'style' => 'visibility:hidden; height:543px;',
            'data-finder-options' => json_encode(['root' => App::system()->config('storage')])
        ], $attributes);

        return sprintf('<textarea%s>%s</textarea>', $this->parseAttributes($attributes), htmlspecialchars($value));
    }

    /**
     * Get html attribute string
     *
     * @param  array $attributes
     * @return string
     */
    protected function parseAttributes($attributes)
    {
        $html = '';

        foreach ($attributes as $name => $val) {
            $html .= is_bool($val) ? " $name" : sprintf(' %s="%s"', $name, htmlspecialchars($val));
        }

        return $html;
    }

    /**
     * Loads the editor.
     */
    public function onEditorLoad(EditorLoadEvent $event)
    {
        if ($event->getEditor()) {
            return;
        }

        $event->setEditor($this);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'editor.load' => ['onEditorLoad', -8]
        ];
    }
}
