<?php

namespace Pagekit\View\Event;

use Symfony\Component\EventDispatcher\Event;

class LayoutEvent extends Event
{
    /**
     * @var string
     */
    protected $layout;

    /**
     * @var array
     */
    protected $parameters;

    /**
     * Constructs an event.
     */
    public function __construct($layout, $parameters = [])
    {
        $this->layout     = $layout;
        $this->parameters = $parameters;
    }

    /**
     * @return string
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * @param string @layout
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param array $parameters
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function setParameter($key, $value)
    {
        $this->parameters[$key] = $value;
    }
}
