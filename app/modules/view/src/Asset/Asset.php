<?php

namespace Pagekit\View\Asset;

abstract class Asset implements AssetInterface, \ArrayAccess
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $source;

    /**
     * @var string
     */
    protected $content;

    /**
     * @var array
     */
    protected $dependencies;

    /**
     * @var array
     */
    protected $options;

    /**
     * Constructor.
     *
     * @param string $name
     * @param string $source
     * @param array  $dependencies
     * @param array  $options
     */
    public function __construct($name, $source, array $dependencies = [], array $options = [])
    {
        $this->name = $name;
        $this->source = $source;
        $this->dependencies = $dependencies;
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getDependencies()
    {
        return $this->dependencies;
    }

    /**
     * {@inheritdoc}
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * {@inheritdoc}
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * {@inheritdoc}
     */
    public function getOption($name)
    {
        return isset($this->options[$name]) ? $this->options[$name] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function setOption($name, $value)
    {
        $this->options[$name] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function dump(array $filters = [])
    {
        $asset = clone $this;

        foreach ($filters as $filter) {
            $filter->filterContent($asset);
        }

        return $asset->getContent();
    }

    /**
     * Sets an option.
     *
     * @param string $name  The option name
     * @param mixed  $value The option value
     */
    public function offsetSet($name, $value)
    {
        $this->options[$name] = $value;
    }

    /**
     * Gets a option value.
     *
     * @param string $name The option name
     *
     * @return mixed The option value
     */
    public function offsetGet($name)
    {
        return isset($this->options[$name]) ? $this->options[$name] : null;
    }

    /**
     * Returns true if the option exists.
     *
     * @param string $name The option name
     *
     * @return bool true if the option exists, false otherwise
     */
    public function offsetExists($name)
    {
        return isset($this->options[$name]);
    }

    /**
     * Removes an option.
     *
     * @param string $name The option name
     */
    public function offsetUnset($name)
    {
        unset($this->options[$name]);
    }
}
