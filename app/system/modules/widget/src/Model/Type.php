<?php

namespace Pagekit\Widget\Model;

abstract class Type implements TypeInterface
{
    protected $id;
    protected $name;
    protected $description;
    protected $defaults;

    public function __construct($id, $name, $description = '', $defaults = [])
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->defaults = $defaults;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
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
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
