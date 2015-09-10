<?php

namespace Pagekit\Widget\Model;

use Pagekit\Module\Module;

class Type extends Module implements TypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function render(Widget $widget)
    {
        if (is_callable($this->get('render'))) {
            return call_user_func($this->get('render'), $widget);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return $this->get(['name', 'label']);
    }
}
