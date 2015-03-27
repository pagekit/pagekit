<?php

namespace Pagekit\Tree\Iterator;

use Pagekit\Tree\Node;

class RecursiveIterator extends \ArrayIterator implements \RecursiveIterator
{
    /**
     * Constructor.
     */
    public function __construct(Node $node)
    {
        parent::__construct($node->getChildren());
    }

    /**
     * Returns if an iterator can be created for the current element.
     *
     * @return bool
     */
    public function hasChildren()
    {
        return $this->current()->hasChildren();
    }

    /**
     * Returns an iterator for the current element.
     *
     * @return mixed
     */
    public function getChildren()
    {
        return new self($this->current());
    }
}
