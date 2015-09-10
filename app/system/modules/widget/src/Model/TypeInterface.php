<?php

namespace Pagekit\Widget\Model;

interface TypeInterface extends \JsonSerializable
{
    /**
     * Renders the widget.
     *
     * @param  Widget $widget
     * @return string
     */
    public function render(Widget $widget);
}
