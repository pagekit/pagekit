<?php

namespace Pagekit\Locale;

use Punic\Data;

class Intl extends Data implements \ArrayAccess
{
    /**
     * @var array
     */
    protected $providers = [
        'calendar'  => 'Punic\Calendar',
        'currency'  => 'Punic\Currency',
        'language'  => 'Punic\Language',
        'number'    => 'Punic\Number',
        'phone'     => 'Punic\Phone',
        'plural'    => 'Punic\Plural',
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
        return static::$instance->offsetGet($name);
    }

    /**
     * Sets a provider.
     *
     * @param string $name
     * @param mixed  $provider
     */
    public function offsetSet($name, $provider)
    {
        $this->providers[$name] = $provider;
    }

    /**
     * Gets a provider.
     *
     * @param  string $name
     * @return mixed
     */
    public function offsetGet($name)
    {
        if (isset($this->providers[$name])) {

            $provider = $this->providers[$name];

            if (is_string($provider)) {
                $this->providers[$name] = new $provider;
            }

            return $this->providers[$name];
        }
    }

    /**
     * Checks if the provider exists.
     *
     * @param  string $name
     * @return bool
     */
    public function offsetExists($name)
    {
        return isset($this->providers[$name]);
    }

    /**
     * Removes a provider.
     *
     * @param string $name
     */
    public function offsetUnset($name)
    {
        unset($this->providers[$name]);
    }
}
