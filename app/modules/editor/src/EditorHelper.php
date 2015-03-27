<?php

namespace Pagekit\Editor;

use Pagekit\Application as App;
use Pagekit\Editor\Event\EditorLoadEvent;

class EditorHelper
{
    /**
     * Render shortcut.
     *
     * @see render()
     */
    public function __invoke($name, $value, array $attributes = [], $parameters = [])
    {
        return $this->render($name, $value, $attributes, $parameters);
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
        if ($editor = App::trigger('editor.load', new EditorLoadEvent)->getEditor()) {
            return $editor->render($value, array_merge($attributes, compact('name')));
        }

        return __('Editor not found.');
    }
}
