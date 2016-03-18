<?php

namespace Pagekit\Ogp;

class Manager implements \ArrayAccess
{

    protected $meta;
    
    protected $prefix;

    protected $collection = [];


    public function __construct($prefix = false)
    {
        $this->prefix = $prefix;
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->collection[] = $value;
        } else {
            $this->collection[$offset] = $value;
        }
    }

    public function offsetExists($offset)
    {
        return isset($this->collection[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->collection[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->collection[$offset]) ? $this->container[$offset] : null;
    }

    public function merge($array)
    {
        $this->collection = array_merge($this->collection, (array) $array);
    }

    public function getValues()
    {
        $values = [];
        
        foreach ($this->collection as $name => $value) {
            
            if ($this->prefix) {
                $name = implode(':', [$this->prefix, $name]);
            }
            
            $values[$name] = $value;
        }
        
        return $values;
    }


}
