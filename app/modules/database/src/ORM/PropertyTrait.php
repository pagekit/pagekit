<?php

namespace Pagekit\Database\ORM;

trait PropertyTrait
{
    /**
     * @var array
     */
    protected static $properties = [];

    /**
     * Gets a object property.
     *
     * @param string $name
     */
    public function __get($name)
    {
        if (isset(static::$properties[$name])) {

            $get = static::$properties[$name]['get'];

            if ($get instanceof \Closure) {
                $get = $get->bindTo($this, $this);
            }

            return call_user_func($get);

        } else {

            trigger_error(sprintf('Undefined property: %s::$%s', __CLASS__, $name), E_USER_NOTICE);
        }
    }

    /**
     * Sets a object property.
     *
     * @param string $name
     * @param mixed  $value
     */
    public function __set($name, $value)
    {
        $set = isset(static::$properties[$name]) ? static::$properties[$name]['set'] : true;

        if (is_callable($set)) {

            if ($set instanceof \Closure) {
                $set = $set->bindTo($this, $this);
            }

            call_user_func($set, $value);

        } elseif ($set === true) {

            $this->$name = $value;
        }
    }

    /**
     * Checks for a object property.
     *
     * @param string $name
     */
    public function __isset($name)
    {
        return isset(static::$properties[$name]);
    }

    /**
     * Sets a object property.
     *
     * @param string $name
     * @param callable $get
     * @param callable|bool $set
     */
    public static function setProperty($name, $get, $set = null)
    {
        static::$properties[$name] = compact('get', 'set');
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

        foreach (static::$properties as $name => $value) {
            $properties[$name] = $object->$name;
        }

        return $properties;
    }
}
