<?php

namespace Pagekit\Widget;

use Pagekit\Application as App;
use Pagekit\Widget\Entity\Widget;
use Pagekit\Widget\Model\WidgetInterface;

class PositionManager implements \JsonSerializable
{
    /**
     * @var array
     */
    protected $assigned = [];

    /**
     * @var array
     */
    protected $registered = [];

    /**
     * @var WidgetInterface[]
     */
    protected $widgets;

    /**
     * Constructor.
     *
     * @param array $assigned
     */
    public function __construct(array $assigned = [])
    {
        $this->assigned = $assigned;
    }

    /**
     * Finds a position by widget id.
     *
     * @param  integer $id
     * @return string
     */
    public function find($id)
    {
        foreach ($this->assigned as $pos => $ids) {
            if (in_array($id, $ids)) {
                return $pos;
            }
        }

        return '';
    }

    /**
     * Assigns widget id(s) to a position.
     *
     * @param  string        $position
     * @param  array|integer $id
     * @return self
     */
    public function assign($position, $id)
    {
        foreach ($this->assigned as $pos => $ids) {
            $this->assigned[$pos] = array_diff($ids, (array) $id);
        }

        if (!$position) {
            return $this;
        }

        if (is_array($id)) {
            $this->assigned[$position] = $id;
        } else {
            $this->assigned[$position][] = $id;
        }

        return $this;
    }

    /**
     * Registers a position.
     *
     * @param string $name
     * @param string $label
     * @param string $description
     */
    public function register($name, $label, $description = '')
    {
        $this->registered[$name] = compact('name', 'label', 'description');
    }

    /**
     * Gets the assigned widget ids.
     *
     * @param  null|string $position
     * @return array
     */
    public function getAssigned($position = null)
    {
        if ($position === null) {
            return $this->assigned;
        }

        return isset($this->assigned[$position]) ? array_values($this->assigned[$position]) : [];
    }

    /**
     * Gets active widgets.
     *
     * @param  string $position
     * @return WidgetInterface[]
     */
    public function getWidgets($position = null)
    {
        if ($this->widgets === null) {

            foreach ($this->getAssigned() as $name => $ids) {

                $widgets = Widget::findAll();
                $module  = App::module('system/widget');
                $node    = App::node()->getId();

                foreach ($ids as $id) {

                    if (!isset($widgets[$id])
                        or !$widget = $widgets[$id]
                        or !$widget->hasAccess(App::user())
                        or ($nodes = $widget->getNodes() and !in_array($node, $nodes))
                        or !$type = $module->getType($widget->getType())
                    ) {
                        continue;
                    }

                    $widget->set('result', $type->render($widget));

                    $this->widgets[$name][] = $widget;

                }

            }

        }

        if ($position === null) {
            return $this->widgets;
        }

        return isset($this->widgets[$position]) ? $this->widgets[$position] : [];
    }

    /**
     * Gets the registered positions.
     *
     * @return array
     */
    public function getRegistered()
    {
        return $this->registered;
    }

    /**
     * Implements JsonSerializable interface.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        $positions = [];

        foreach ($this->registered as $name => $pos) {
            $positions[] = array_merge($pos, ['assigned' => $this->getAssigned($name)]);
        }

        foreach (array_diff_key($this->assigned, $this->registered) as $name => $ids) {
            $positions[] = ['name' => $name, 'label' => $name, 'description' => '', 'assigned' => $this->assigned($name)];
        }

        return $positions;
    }
}
