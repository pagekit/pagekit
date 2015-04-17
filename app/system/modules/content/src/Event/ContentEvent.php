<?php

namespace Pagekit\Content\Event;

use Pagekit\Event\Event;

class ContentEvent extends Event
{
    /**
     * @var string
     */
    protected $content;

    /**
     * @var array
     */
    protected $plugins = [];

    /**
     * Constructor.
     *
     * @param string $name
     * @param string $content
     * @param array  $parameters
     */
    public function __construct($name, $content, array $parameters = [])
    {
        parent::__construct($name, $parameters);

        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return array
     */
    public function getPlugins()
    {
        return $this->plugins;
    }

    /**
     * @param  string $name
     * @return mixed
     */
    public function getPlugin($name)
    {
        return isset($this->plugins[$name]) ? $this->plugins[$name] : null;
    }

    /**
     * @param string $name
     * @param mixed  $callback
     */
    public function addPlugin($name, $callback)
    {
        $this->plugins[$name] = $callback;
    }
}
