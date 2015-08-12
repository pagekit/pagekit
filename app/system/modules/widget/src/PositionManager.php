<?php

namespace Pagekit\Widget;

use Pagekit\Application as App;
use Pagekit\Config\Config;

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
        $positions = $this->all();
        return isset($positions[$name]) ? $positions[$name] : null;
    }

    /**
     * Gets menus.
     *
     * @return array
     */
    public function all()
    {
        array_walk($this->positions, function(&$position, $name) {
            $position['assigned'] = $this->config->get("_positions.$name", []);
        });

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
        $this->positions[$name] = compact('name', 'label');
    }

    /**
     * Finds a theme position by widget id.
     *
     * @param  integer $id
     * @return string
     */
    public function find($id)
    {
        foreach ($this->all() as $name => $position) {
            if (in_array($id, $position['assigned'])) {
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

        $this->config->set('_positions', $positions);
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return $this->all();
    }
}
