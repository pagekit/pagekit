<?php

namespace Pagekit\View\Event;

use Symfony\Component\EventDispatcher\Event;

class RenderEvent extends Event
{
    /**
     * @var string
     */
    protected $result;

    /**
     * @var string
     */
    protected $template;

    /**
     * @var array
     */
    protected $parameters;

    /**
     * Constructor.
     */
    public function __construct($template, array $parameters = [])
    {
        $this->template   = $template;
        $this->parameters = $parameters;
    }

    /**
     * @return string
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param string $result
     */
    public function setResult($result)
    {
        $this->result = $result;
    }

    /**
     * @param string $result
     */
    public function addResult($result)
    {
        $this->result .= $result;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param string $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
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
     * @param  string $key
     * @return mixed  $value
     */
    public function getParameter($key)
    {
        return isset($this->parameters[$key]) ? $this->parameters[$key] : null;
    }

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function setParameter($key, $value)
    {
        $this->parameters[$key] = $value;
    }

    /**
     * @param  string $name
     * @return self
     */
    public function dispatch($name)
    {
       return $this->getDispatcher()->dispatch($name, $this);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->result;
    }
}
