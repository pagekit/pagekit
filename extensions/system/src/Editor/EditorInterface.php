<?php

namespace Pagekit\Editor;

interface EditorInterface
{
    /**
     * Renders the editor
     *
     * @param  string $value
     * @return string
     */
    public function render($value);
}
