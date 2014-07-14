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
     * Get widget description - optionally with additional information from current widget.
     *
     * @param  WidgetInterface $widget
     * @return string
     */
    public function getDescription(WidgetInterface $widget = null);

    /**
     * Render the widget.
     *
     * @param WidgetInterface $widget
     * @param array           $options
     */
    public function render(WidgetInterface $widget, $options = []);

    /**
     * Render widget form.
     *
     * @param WidgetInterface $widget
     */
    public function renderForm(WidgetInterface $widget);
}
