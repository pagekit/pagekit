<?php

namespace Pagekit\Site\Event;

use Pagekit\Application\Event;

class TypeEvent extends Event implements \IteratorAggregate
{
    /**
     * Registers a node type.
     *
     * @param string $id
     * @param string $label
     * @param array  $options
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
