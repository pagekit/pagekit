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
        if (isset(static::$properties[$name]['get'])) {

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
     * Sets a dynamic property.
     *
     * @param string $name
     * @param mixed  $value
     */
    public function __set($name, $value)
    {
        if (isset(static::$properties[$name]['set'])) {

            $set = static::$properties[$name]['set'];

            if ($set instanceof \Closure) {
                $set = $set->bindTo($this, $this);
            }

            call_user_func($set, $value);

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
