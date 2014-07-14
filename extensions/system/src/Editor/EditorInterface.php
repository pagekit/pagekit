<?php

namespace Pagekit\Editor;

interface EditorInterface
{
    /**
     * Renders the editor
     *
     * @param  string $value
     * @param  array  $attributes
     * @return string
     */
    public function render($value, array $attributes = []);
}
