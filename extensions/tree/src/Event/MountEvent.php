<?php

namespace Pagekit\Tree\Event;

use Pagekit\Framework\Event\Event;

class MountEvent extends Event implements \IteratorAggregate
{
    /**
     * Register a mount point.
     *
     * @param  string $id
     * @param  string $label
     */
    public function register($id, $label)
    {
        $this->parameters[$id] = $label;
    }

    /**
     * Get list of mount points.
     *
     * @return array
     */
    public function getMountPoints()
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
