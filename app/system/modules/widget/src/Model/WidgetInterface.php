<?php

namespace Pagekit\Widget\Model;

interface WidgetInterface extends \JsonSerializable
{
    /**
     * Returns the widget id.
     *
     * @return mixed
     */
    public function getId();

    /**
     * Returns the title.
     *
     * @return string
     */
    public function getTitle();

    /**
     * Returns the type.
     *
     * @return string $type
     */
    public function getType();

    /**
     * Returns the widget settings.
     *
     * @return array $settings
     */
    public function getSettings();

    /**
     * Returns a widget setting or the given default value if no value is found.
     *
     * @param  string $name
     * @param  mixed  $default
     * @return mixed
     */
    public function get($name, $default = null);

    /**
     * Sets a widget setting.
     *
     * @param string  $name
     * @param mixed  $value
     */
    public function set($name, $value);

    /**
     * Removes a widget setting.
     *
     * @param string $name
     */
    public function remove($name);

    /**
     * Returns the rendered widget output, otherwise null.
     *
     * @param  array $options
     * @return string|null
     */
    public function render($options = []);

    /**
     * Gets the default string representation of this widget.
     */
    public function __toString();
}
