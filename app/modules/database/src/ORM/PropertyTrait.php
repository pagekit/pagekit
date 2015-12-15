<?php

namespace Pagekit\Database\ORM;

trait PropertyTrait
{
    /**
     * @var array
     */
    protected static $_properties = [];

    /**
     * Gets an object property.
     *
     * @param  string $name
     * @return mixed
     */
    public function __get($name)
    {
        if ($descriptor = static::getPropertyDescriptor($name)) {

            $get = $descriptor['get'];

            if (is_string($get)) {
                $get = [$this, $get];
            } elseif ($get instanceof \Closure) {
                $get = $get->bindTo($this, $this);
            }

            return call_user_func($get);

        } else {

            trigger_error(sprintf('Undefined property: %s::$%s', __CLASS__, $name), E_USER_NOTICE);
        }
    }

    /**
     * Sets an object property.
     *
     * @param string $name
     * @param mixed  $value
     */
    public function __set($name, $value)
    {
        if ($descriptor = static::getPropertyDescriptor($name)) {

            $set = $descriptor['set'];

            if (is_string($set)) {
                $set = [$this, $set];
            } elseif ($set instanceof \Closure) {
                $set = $set->bindTo($this, $this);
            }

            if (is_callable($set)) {
                call_user_func($set, $value);
            } elseif ($set === true) {
                $this->$name = $value;
            }

        } else {

            $this->$name = $value;
        }
    }

    /**
     * Clones the object properties.
     */
    public function __clone() {
        foreach (static::$_properties as $name => $value) {
            $this->$name = $this->__get($name);
        }
    }

    /**
     * Checks for an object property.
     *
     * @param  string $name
     * @return bool
     */
    public function __isset($name)
    {
        return isset(static::$_properties[$name]);
    }

    /**
     * Gets all object properties.
     *
     * @param  mixed $object
     * @return array
     */
    public static function getProperties($object)
    {
        $properties = get_object_vars($object);

        foreach (array_diff_key(static::$_properties, $properties) as $name => $value) {
            $properties[$name] = $object->$name;
        }

        if (isset(static::$properties)) {
            foreach (array_diff_key(static::$properties, $properties) as $name => $value) {
                $properties[$name] = $object->$name;
            }
        }

        return $properties;
    }

    /**
     * Defines an object property.
     *
     * @param  string                $name
     * @param  string|callable|array $get
     * @param  string|callable|bool  $set
     * @return array
     */
    public static function defineProperty($name, $get, $set = null)
    {
        $descriptor = is_array($get) ? $get : compact('get', 'set');

        if (isset($descriptor[0])) {
            $descriptor['get'] = $descriptor[0];
        }

        if (isset($descriptor[1])) {
            $descriptor['set'] = $descriptor[1];
        }

        unset($descriptor[0], $descriptor[1]);

        return static::$_properties[$name] = $descriptor;
    }

    /**
     * Gets an object property descriptor.
     *
     * @param  string $name
     * @return array
     */
    protected static function getPropertyDescriptor($name)
    {
        if (isset(static::$_properties[$name])) {
            return static::$_properties[$name];
        }

        if (isset(static::$properties, static::$properties[$name])) {
            return static::defineProperty($name, static::$properties[$name]);
        }
    }
}
