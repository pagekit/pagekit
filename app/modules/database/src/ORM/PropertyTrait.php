<?php

namespace Pagekit\Database\ORM;

trait PropertyTrait
{
    /**
     * @var array
     */
    protected static $properties = [];

    /**
     * Gets a dynamic property.
     *
     * @param string $name
     */
    public function __get($name)
    {
        if (isset(static::$properties[$name])) {
            return call_user_func(static::$properties[$name]['get']);
        } else {
            trigger_error(sprintf('Undefined property: %s::$%s', __CLASS__, $name), E_USER_NOTICE);
        }
    }

    /**
     * Sets a dynamic property.
     *
     * @param string $name
     * @param mixed  $value
     */
    public function __set($name, $value)
    {
        if (isset(static::$properties[$name]['set'])) {
            call_user_func(static::$properties[$name]['set'], $value);
        } else {
            $this->$name = $value;
        }
    }

    /**
     * Checks for a dynamic property.
     *
     * @param string $name
     */
    public function __isset($name)
    {
        return isset(static::$properties[$name]);
    }

    /**
     * Sets a dynamic property.
     *
     * @param string   $name
     * @param callable $get
     * @param callable $set
     */
    public static function property($name, callable $get, callable $set = null)
    {
        static::$properties[$name] = compact('get', 'set');
    }
}
