<?php

namespace Pagekit\Widget;

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

        if (is_array($id)) {
            $this->assigned[$position] = $id;
        } else {
            $this->assigned[$position][] = $id;
        }

        return $this;
    }

    /**
     * Gets assigned widget ids.
     *
     * @param  string $position
     * @return array
     */
    public function assigned($position)
    {
        return isset($this->assigned[$position]) ? array_values($this->assigned[$position]) : [];
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
     * @return array
     */
    public function getAssigned()
    {
        return $this->assigned;
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
            $positions[] = array_merge($pos, ['assigned' => $this->assigned($name)]);
        }

        foreach (array_diff_key($this->assigned, $this->registered) as $name => $ids) {
            $positions[] = ['name' => $name, 'label' => $name, 'description' => '', 'assigned' => $this->assigned($name)];
        }

        return $positions;
    }
}
