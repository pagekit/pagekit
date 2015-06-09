<?php

namespace Pagekit\Widget\Model;

use Pagekit\Application as App;
use Pagekit\Util\Arr;

abstract class Widget implements WidgetInterface
{
    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var array
     */
    protected $settings = [];

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the widget id.
     *
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Sets the name.
     *
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the title.
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * {@inheritdoc}
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * Sets the widget settings.
     *
     * @param array $settings
     */
    public function setSettings(array $settings = [])
    {
        $this->settings = $settings;
    }

    /**
     * Merges the passed settings.
     *
     * @param array $settings
     */
    public function mergeSettings(array $settings = [])
    {
        $this->setSettings(Arr::merge($this->settings, $settings));
    }

    /**
     * Merges the passed settings.
     *
     * @param array $settings
     */
    public function setDefaults(array $settings = [])
    {
        $this->setSettings(Arr::merge($settings, $this->settings));
    }

    /**
     * {@inheritdoc}
     */
    public function get($name, $default = null)
    {
        return Arr::get($this->settings, $name, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function set($name, $value)
    {
        Arr::set($this->settings, $name, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($name)
    {
        Arr::remove($this->settings, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function render($options = [])
    {
        $type = App::module('system/widget')->getType($this->type);

        return $type ? $type->render($this, $options) : '';
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->render();
    }
}
