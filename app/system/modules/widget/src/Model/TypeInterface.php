<?php

namespace Pagekit\Widget\Model;

interface TypeInterface extends \JsonSerializable
{
    /**
     * Gets widget id.
     *
     * @return mixed
     */
    public function getId();

    /**
     * Gets widget name.
     *
     * @return string
     */
    public function getName();

    /**
     * Gets widget description - optionally with additional information from current widget.
     *
     * @param  WidgetInterface $widget
     * @return string
     */
    public function getDescription(WidgetInterface $widget = null);

    /**
     * Gets the widget default settings.
     *
     * @return array
     */
    public function getDefaults();

    /**
     * Renders the widget.
     *
     * @param WidgetInterface $widget
     * @param array           $options
     */
    public function render(WidgetInterface $widget, $options = []);

    /**
     * Renders widget form.
     *
     * @param WidgetInterface $widget
     */
    public function renderForm(WidgetInterface $widget);
}
