<?php

namespace Pagekit\System\Widget;

use Pagekit\System\Widget\Model\TypeInterface;
use Pagekit\System\Widget\Model\TypeManager;
use Pagekit\System\Widget\Model\WidgetInterface;

class WidgetProvider implements \IteratorAggregate
{
    /**
     * @var mixed
     */
    protected $widgets;

    /**
     * @var TypeManager
     */
    protected $types;

    /**
     * Constructor.
     *
     * @param mixed        $widgets
     * @param TypeManager  $types
     */
    public function __construct($widgets, TypeManager $types)
    {
        $this->widgets = $widgets;
        $this->types   = $types;
    }

    /**
     * Get a widget instance.
     *
     * @param  string $id
     * @return WidgetInterface
     */
    public function get($id)
    {
        return $this->widgets->find($id);
    }

    /**
     * Check if a widget type is registered.
     *
     * @param  string $id
     * @return boolean
     */
    public function hasType($id)
    {
        return $this->types->has($id);
    }

    /**
     * Get a widget type instance.
     *
     * @param  string $id
     * @return TypeInterface
     */
    public function getType($id)
    {
        return $this->types->get($id);
    }

    /**
     * Register a widget type.
     *
     * @param TypeInterface|string $type
     */
    public function registerType($type)
    {
        $this->types->register($type);
    }

    /**
     * Unregister a widget type.
     *
     * @param string $id
     */
    public function unregisterType($id)
    {
        $this->types->unregister($id);
    }

    /**
     * Returns the rendered widget output, otherwise null.
     *
     * @param  mixed $widget
     * @param  array $options
     * @return string|null
     */
    public function render($widget, $options = array())
    {
        if (!$widget instanceof WidgetInterface) {
            $widget = $this->get($widget);
        }

        if ($widget && $this->hasType($type = $widget->getType())) {
            return $this->getType($type)->render($widget, $options);
        }
    }

    /**
     * Implements the IteratorAggregate.
     */
    public function getIterator()
    {
        return $this->types->getIterator();
    }

    /**
     * @return mixed
     */
    public function getWidgetRepository()
    {
        return $this->widgets;
    }

    /**
     * @return TypeManager
     */
    public function getTypeManager()
    {
        return $this->types;
    }
}
