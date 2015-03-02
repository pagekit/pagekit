<?php

namespace Pagekit\Widget\Model;

abstract class Type implements TypeInterface, \JsonSerializable
{
    public function jsonSerialize()
    {
        return ['id' => $this->getId(), 'name' => $this->getName()];
    }
}
