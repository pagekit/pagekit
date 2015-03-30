<?php

namespace Pagekit\Application\Traits;

trait StaticTrait
{
    protected static $instance;

    /**
     * Gets a container instance.
     *
     * @return Container
     */
    public static function getInstance()
    {
        return static::$instance;
    }

    /**
     * Checks if a parameter or service is defined.
     *
     * @param  string $name
     * @return bool
     */
    public static function has($name)
    {
        return static::$instance->offsetExists($name);
    }

    /**
     * Gets a parameter or service.
     *
     * @param  string $name
     * @return mixed
     */
    public static function get($name)
    {
        return static::$instance->offsetGet($name);
    }

    /**
     * Sets a parameter or service.
     *
     * @param string $name
     * @param mixed  $value
     */
    public static function set($name, $value)
    {
        static::$instance->offsetSet($name, $value);
    }

    /**
     * Removes a parameter or service.
     *
     * @param string $name
     */
    public static function remove($name)
    {
        static::$instance->offsetUnset($name);
    }

    /**
     * Magic method to access the container in a static context.
     *
     * @param  string $name
     * @param  array  $args
     * @return mixed
     */
    public static function __callStatic($name, $args)
    {
        $value = static::$instance->offsetGet($name);

        return $args ? call_user_func_array($value, $args) : $value;
    }
}
