<?php

namespace Pagekit\Widget;

class PositionManager implements \ArrayAccess, \IteratorAggregate, \JsonSerializable
{
    /**
     * @var array
     */
    protected $positions;

    /**
     * Constructor.
     *
     * @param array $positions
     */
    public function __construct(array $positions)
    {
        $this->positions = $positions;
    }

    /**
     * Gets assigned widget ids.
     *
     * @param  string $position
     * @return array
     */
    public function get($position)
    {
        return isset($this->positions[$position]) ? $this->positions[$position] : [];
    }

    /**
     * Sets widget id to a position.
     *
     * @param  string $position
     * @param  int    $id
     * @return self
     */
    public function set($position, $id)
    {
        foreach ($this->positions as $pos => $ids) {
            $this->positions[$pos] = array_diff($ids, [$id]);
        }

        $this->positions[$position][] = $id;

        return $this;
    }

    /**
     * Finds a position by widget id.
     *
     * @param  integer $id
     * @return string
     */
    public function find($id)
    {
        foreach ($this->positions as $pos => $ids) {
            if (in_array($id, $ids)) {
                return $pos;
            }
        }

        return '';
    }

    /**
     * Implements ArrayAccess interface.
     */
    public function offsetExists($position)
    {
        return array_key_exists($position, $this->positions);
    }

    /**
     * Implements ArrayAccess interface.
     *
     * @see get()
     */
    public function offsetGet($position)
    {
        return $this->get($position);
    }

    /**
     * Implements ArrayAccess interface.
     *
     * @see set()
     */
    public function offsetSet($position, $id)
    {
        $this->set($position, $id);
    }

    /**
     * Implements ArrayAccess interface.
     */
    public function offsetUnset($position)
    {
        unset($this->positions[$position]);
    }

    /**
     * Implements the IteratorAggregate interface.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->positions);
    }

    /**
     * Implements JsonSerializable interface.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        $positions = [];

        foreach ($this->positions as $pos => $ids) {
            if ($ids) {
                $positions[$pos] = $ids;
            }
        }

        return $positions;
    }
}
