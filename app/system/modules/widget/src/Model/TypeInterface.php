<?php

namespace Pagekit\Widget\Model;

interface TypeInterface extends \JsonSerializable
{
    /**
     * Renders the widget.
     *
     * @param  WidgetInterface $widget
     * @return string
     */
    public function render(WidgetInterface $widget);
}
