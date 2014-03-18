<?php

namespace Pagekit\System\Event;

use Pagekit\Framework\Event\Event;

class EditorLoadEvent extends Event
{
    /**
     * @var array
     */
    protected $attributes;

    /**
     * @var array
     */
    protected $plugins = array();

    /**
     * Constructor.
     *
     * @param array $attributes
     * @param array $parameters
     */
    function __construct(array $attributes, array $parameters)
    {
        parent::__construct($parameters);

        $this->attributes = $attributes;
    }

    /**
     * @return array
     */
    public function getEditor()
    {
        return $this['editor'] ?: 'markdown';
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

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param array $attributes
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * @param array $attributes
     */
    public function addAttributes(array $attributes)
    {
        $this->attributes = array_merge($this->attributes, $attributes);
    }
}
