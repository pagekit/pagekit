<?php

namespace Pagekit\Editor;

use Pagekit\Application as App;
use Pagekit\View\Helper\Helper;

class EditorHelper extends Helper
{
    /**
     * Render shortcut.
     *
     * @see render()
     */
    public function __invoke($name, $value, array $attributes = [])
    {
        return $this->render($name, $value, $attributes);
    }

    /**
     * Renders an editor.
     *
     * @param  string $name
     * @param  string $value
     * @param  array  $attributes
     * @return string
     */
    public function render($name, $value, array $attributes = [])
    {
        $attributes = array_merge(['name' => $name, 'autocomplete' => 'off', 'style' => 'visibility:hidden; height:543px;'], $attributes);
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
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'editor';
    }
}
