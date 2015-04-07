<?php

namespace Pagekit\Site\Model;

use Pagekit\Application as App;

abstract class Type implements TypeInterface
{
    protected $id;
    protected $label;
    protected $options;

    public function __construct($id, $label, array $options = [])
    {
        $this->id      = $id;
        $this->label   = $label;
        $this->options = $options;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
