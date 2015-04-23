<?php

namespace Pagekit\Intl;

use Punic\Data;

class Intl extends Data implements \ArrayAccess
{
    /**
     * @var array
     */
    protected $helpers = [
        'calendar'  => 'Punic\Calendar',
        'currency'  => 'Punic\Currency',
        'language'  => 'Punic\Language',
        'number'    => 'Punic\Number',
        'territory' => 'Punic\Territory',
        'unit'      => 'Punic\Unit'
    ];

    /**
     * @var Intl
     */
    protected static $instance;

    /**
     * Gets an instance.
     *
     * @return Intl
     */
    public static function getInstance()
    {
        if (!static::$instance) {
            static::$instance = new static;
        }

        return static::$instance;
    }

    /**
     * Get shortcut.
     *
     * @see offsetGet()
     */
    public function __invoke($name)
    {
        return $this->offsetGet($name);
    }

    /**
     * Magic method to access the class in a static context.
     *
     * @param  string $name
     * @param  array  $args
     * @return mixed
     */
    public static function __callStatic($name, $args)
    {
        $helper = static::getInstance()->offsetGet($name);

        return $args ? call_user_func_array($helper, $args) : $helper;
    }

    /**
     * Sets a helper.
     *
     * @param string $name
     * @param mixed  $helper
     */
    public function offsetSet($name, $helper)
    {
        $this->helpers[$name] = $helper;
    }

    /**
     * Gets a helper.
     *
     * @param  string $name
     * @return mixed
     */
    public function offsetGet($name)
    {
        if (isset($this->helpers[$name])) {

            $helper = $this->helpers[$name];

            if (is_string($helper)) {
                $this->helpers[$name] = new $helper;
            }

            return $this->helpers[$name];
        }
    }

    /**
     * Checks if the helper exists.
     *
     * @param  string $name
     * @return bool
     */
    public function offsetExists($name)
    {
        return isset($this->helpers[$name]);
    }

    /**
     * Removes a helper.
     *
     * @param string $name
     */
    public function offsetUnset($name)
    {
        unset($this->helpers[$name]);
    }
}
