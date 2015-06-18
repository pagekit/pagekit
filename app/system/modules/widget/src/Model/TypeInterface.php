<?php

namespace Pagekit\Widget\Model;

interface TypeInterface extends \JsonSerializable
{
    /**
     * Renders the widget.
     *
     * @param WidgetInterface $widget
     * @param array           $options
     */
    public function render(WidgetInterface $widget, $options = []);
}
