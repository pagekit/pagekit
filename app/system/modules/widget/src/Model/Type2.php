<?php

namespace Pagekit\Widget\Model;

use Pagekit\Module\Module;

class Type2 extends Module implements TypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription(WidgetInterface $widget = null)
    {
        return $this->description;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaults()
    {
        return $this->defaults;
    }

    /**
     * {@inheritdoc}
     */
    public function render(WidgetInterface $widget, $options = [])
    {
        if (is_callable($this->render)) {
            return call_user_func($this->render, $widget, $options);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
