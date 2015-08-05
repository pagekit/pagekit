<?php

namespace Pagekit\Widget;

use Pagekit\Application as App;
use Pagekit\Config\Config;
use Pagekit\Widget\Model\Widget;

class PositionManager implements \JsonSerializable
{
    protected $positions = [];
    protected $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Get shortcut.
     *
     * @see get()
     */
    public function __invoke($name)
    {
        return $this->get($name);
    }

    /**
     * Gets position by name.
     *
     * @param  string $name
     * @return array
     */
    public function get($name)
    {
        return isset($this->positions[$name]) ? $this->positions[$name] : null;
    }

    /**
     * Gets menus.
     *
     * @return array
     */
    public function all()
    {
        return $this->positions;
    }

    /**
     * Registers a position.
     *
     * @param string $name
     * @param string $label
     */
    public function register($name, $label)
    {
        $this->positions[$name] = [
            'name' => $name,
            'label' => $label,
            'assigned' => $this->config->get("_positions.$name", [])
        ];
    }

    /**
     * Finds a theme position by widget id.
     *
     * @param  integer $id
     * @return string
     */
    public function find($id)
    {
        foreach ($this->config->get('_positions', []) as $name => $assigned) {
            if (in_array($id, $assigned)) {
                return $name;
            }
        }

        return '';
    }

    /**
     * Assigns widgets to a theme position.
     *
     * @param string        $position
     * @param array|integer $id
     */
    public function assign($position, $id)
    {
        $positions = $this->config->get('_positions', []);

        if (!is_array($id) && $position === $this->find($id)) {
            return;
        }

        foreach ($positions as $name => $assigned) {
            $positions[$name] = array_values(array_diff($assigned, (array) $id));
        }

        if (is_array($id)) {
            $positions[$position] = array_values(array_unique($id));
        } else {
            $positions[$position][] = $id;
        }

        $this->positions[$position]['assigned'] = $positions[$position];
        $this->config->set('_positions', $positions);
    }

    /**
     * Gets active widgets.
     *
     * @param  string|null $position
     * @return Widget[]
     */
    public function findActive($position)
    {
        static $widgets, $positions = [];

        if (null === $widgets) {
            $widgets = Widget::where(['status' => 1])->get();
        }

        if (!isset($this->positions[$position])) {
            return [];
        }

        if (!isset($positions[$position])) {

            $positions[$position] = [];

            foreach ($this->positions[$position]['assigned'] as $id) {

                if (!isset($widgets[$id])
                    or !$widget = $widgets[$id]
                    or !$widget->hasAccess(App::user())
                    or ($nodes = $widget->nodes and !in_array(App::node()->id, $nodes))
                    or !$type = App::widget()->getType($widget->type)
                    or !$result = $type->render($widget)
                ) {
                    continue;
                }

                $widget->set('result', $result);
                $positions[$position][] = $widget;
            }
        }

        return $positions[$position];
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return $this->all();
    }
}
