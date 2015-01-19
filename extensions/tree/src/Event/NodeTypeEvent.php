<?php

namespace Pagekit\Tree\Event;

use Pagekit\Application\Event;

class NodeTypeEvent extends Event implements \IteratorAggregate
{
    /**
     * Registers a node type.
     *
     * @param string $id
     * @param string $label
     */
    public function register($id, $label, array $options = [])
    {
        $this->parameters[$id] = array_merge($options, compact('id', 'label'));
    }

    /**
     * Get list of node types.
     *
     * @return array
     */
    public function getTypes()
    {
        return $this->parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->parameters);
    }
}
