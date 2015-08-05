<?php

namespace Pagekit\Event;

use Pagekit\Util\Arr;

class Event implements EventInterface, \ArrayAccess
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $parameters;

    /**
     * @var bool
     */
    protected $propagationStopped = false;

    /**
     * @var EventDispatcher
     */
    protected $dispatcher;

    /**
     * Constructor.
     *
     * @param string $name
     * @param array  $parameters
     */
    public function __construct($name, array $parameters = [])
    {
        $this->name = $name;
        $this->parameters = $parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the event name.
     *
     * @param  string $name
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Gets all parameters.
     *
     * @return array|object|\ArrayAccess
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Sets all parameters.
     *
     * @param  array
     * @return self
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * @param  mixed $values
     * @param  bool  $replace
     * @return self
     */
    public function addParameters(array $values, $replace = false)
    {
        $this->parameters = Arr::merge($this->parameters, $values, $replace);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * Sets the event dispatcher.
     *
     * @param EventDispatcherInterface $dispatcher
     */
    public function setDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function isPropagationStopped()
    {
        return $this->propagationStopped;
    }

    /**
     * {@inheritdoc}
     */
    public function stopPropagation()
    {
        $this->propagationStopped = true;
    }

    /**
     * Checks if a parameter exists.
     *
     * @param  string $name
     * @return mixed
     */
    public function offsetExists($name)
    {
        return isset($this->parameters[$name]);
    }

    /**
     * Gets a parameter.
     *
     * @param  string $name
     * @return mixed
     */
    public function offsetGet($name)
    {
        return isset($this->parameters[$name]) ? $this->parameters[$name] : null;
    }

    /**
     * Sets a parameter.
     *
     * @param  string   $name
     * @param  callable $callback
     */
    public function offsetSet($name, $callback)
    {
        $this->parameters[$name] = $callback;
    }

    /**
     * Unsets a parameter.
     *
     * @param string $name
     */
    public function offsetUnset($name)
    {
        unset($this->parameters[$name]);
    }
}
