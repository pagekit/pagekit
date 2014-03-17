<?php

namespace Pagekit\System\Event;

use Pagekit\Framework\Event\Event;

class RegisterJsonEvent extends Event
{
    /**
     * @var string[]
     */
    private $sources = array();

    /**
     * Checks if a source is registered.
     *
     * @param  string $name
     * @return boolean
     */
    public function has($name)
    {
        return isset($this->sources[$name]);
    }

    /**
     * Returns a source.
     *
     * @param  string $name
     * @return string
     */
    public function get($name)
    {
        return $this->has($name) ? $this->sources[$name] : null;
    }

    /**
     * Registers source.
     *
     * @param  string $name
     * @param  string $source
     */
    public function register($name, $source)
    {
        $this->sources[$name] = $source;
    }

    /**
     * Unregisters a source.
     *
     * @param  string  $name
     */
    public function unregister($name)
    {
        unset($this->sources[$name]);
    }
}
