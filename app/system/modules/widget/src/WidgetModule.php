<?php

namespace Pagekit\Widget;

use Pagekit\Application as App;
use Pagekit\Module\Module;
use Pagekit\Widget\Entity\Widget;
use Pagekit\Widget\Model\TypeInterface;
use Pagekit\Widget\Model\WidgetInterface;

class WidgetModule extends Module
{
    protected $types = [];
    protected $positions = [];
    protected $widgets;

    public function main(App $app)
    {
        // $this->config->merge(['widget' => ['defaults' => $app['theme']->config('widget.defaults', [])]]);

        $app['module']->addFactory('widget', function ($module) use ($app) {

            $class = is_string($module['main']) ? $module['main'] : 'Pagekit\Widget\Model\Type';

            $module = new $class($module);
            $module->main($app);

            $this->registerType($module->name, $module);

            return $module;
        });

        $app['module']->addLoader(function ($name, $module) use ($app) {

            if (isset($module['positions'])) {
                foreach ($module['positions'] as $name => $position) {
                    list($label, $description) = array_merge((array) $position, ['']);
                    $this->registerPosition($name, $label, $description);
                }
            }

            return $module;
        });

        $app['widget'] = $this;
    }

    /**
     * @param  string $type
     * @return TypeInterface
     */
    public function getType($type)
    {
        return isset($this->types[$type]) ? $this->types[$type] : null;
    }

    /**
     * @return array
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * Registers a type.
     *
     * @param string        $name
     * @param TypeInterface $type
     */
    public function registerType($name, TypeInterface $type)
    {
        $this->types[$name] = $type;
    }

    /**
     * Assigns widget id(s) to a position.
     *
     * @param  string        $position
     * @param  array|integer $id
     * @return integer[]
     */
    public function assign($position, $id)
    {
        if (!is_array($id) && $position === $this->findPosition($id)) {
            return $this->positions[$position]['assigned'];
        }

        foreach ($this->positions as $pos) {
            $pos['assigned'] = array_diff($pos['assigned'], (array) $id);
        }

        if (!$position) {
            return [];
        }

        if (is_array($id)) {
            $this->positions[$position]['assigned'] = array_unique($id);
        } else {
            $this->positions[$position]['assigned'][] = $id;
        }

        return $this->positions[$position]['assigned'];
    }

    /**
     * Finds a position by widget id.
     *
     * @param  integer $id
     * @return string
     */
    public function findPosition($id)
    {
        foreach ($this->positions as $name => $position) {
            if (in_array($id, $position['assigned'])) {
                return $name;
            }
        }

        return '';
    }

    /**
     * Registers a position.
     *
     * @param string $name
     * @param string $label
     * @param string $description
     */
    public function registerPosition($name, $label, $description = '')
    {
        $assigned = (array) $this->config('widget.positions.'.$name, []);
        $this->positions[$name] = compact('name', 'label', 'description', 'assigned');
    }

    /**
     * Gets the registered positions.
     *
     * @return array
     */
    public function getPositions()
    {
        return array_values($this->positions);
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

            foreach ($this->positions->getAssigned() as $name => $ids) {

                $widgets = Widget::findAll();
                $node    = App::node()->getId();

                foreach ($ids as $id) {

                    if (!isset($widgets[$id])
                        or !$widget = $widgets[$id]
                        or !$widget->hasAccess(App::user())
                        or ($nodes = $widget->getNodes() and !in_array($node, $nodes))
                        or !$type = $this->getType($widget->getType())
                        or !$result = $type->render($widget)
                    ) {
                        continue;
                    }

                    $widget->set('result', $result);

                    $this->widgets[$name][] = $widget;

                }

            }

        }

        if ($position === null) {
            return $this->widgets;
        }

        return isset($this->widgets[$position]) ? $this->widgets[$position] : [];
    }
}
