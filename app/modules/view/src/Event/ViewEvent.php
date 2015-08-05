<?php

namespace Pagekit\View\Event;

use Pagekit\Event\Event;

class ViewEvent extends Event
{
    /**
     * @var string
     */
    protected $template;

    /**
     * @var string
     */
    protected $result;

    /**
     * Constructor.
     *
     * @param string $name
     * @param string $template
     * @param array  $parameters
     */
    public function __construct($name, $template, array $parameters = [])
    {
        parent::__construct($name, $parameters);

        $this->template = $template;
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
    public function __toString()
    {
        return $this->result;
    }
}
