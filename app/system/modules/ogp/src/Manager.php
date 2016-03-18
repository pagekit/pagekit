<?php

namespace Pagekit\Ogp;

class Manager implements \ArrayAccess
{

    protected $collection = [];

    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->collection[] = $value;
        } else {
            $this->collection[$offset] = $value;
        }
    }

    public function offsetExists($offset) {
        return isset($this->collection[$offset]);
    }

    public function offsetUnset($offset) {
        unset($this->collection[$offset]);
    }

    public function offsetGet($offset) {
        return isset($this->collection[$offset]) ? $this->container[$offset] : null;
    }

}
