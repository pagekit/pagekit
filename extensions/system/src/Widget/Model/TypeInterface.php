<?php

namespace Pagekit\Widget\Model;

interface TypeInterface
{
    /**
     * Get widget id.
     *
     * @return mixed
     */
    public function getId();

    /**
     * Get widget name.
     *
     * @return string
     */
    public function getName();

    /**
     * Get widget description.
     *
     * @return string
     */
    public function getDescription();

    /**
     * Returns information of the current widget object.
     *
     * @return string
     */
    public function getInfo(WidgetInterface $widget);

    /**
     * Render the widget.
     *
     * @param WidgetInterface $widget
     * @param array           $options
     */
    public function render(WidgetInterface $widget, $options = array());

    /**
     * Render widget form.
     *
     * @param WidgetInterface $widget
     */
    public function renderForm(WidgetInterface $widget);
}
