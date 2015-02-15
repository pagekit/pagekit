<?php

namespace Pagekit\Tree;

use Pagekit\Tree\Iterator\RecursiveIterator;

class Node implements \IteratorAggregate, \Countable
{
    /**
     * @var Node|null
     */
    protected $parent;

    /**
     * @var Node[]
     */
    protected $children = [];

    /**
     * @return Node|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Sets the parent node.
     *
     * @param  Node|null $parent
     * @return self
     *
     * @throws \InvalidArgumentException
     */
    public function setParent(Node $parent = null)
    {
        if ($parent === $this) {
            throw new \InvalidArgumentException('A node cannot have itself as a parent');
        }

        if ($parent === $this->parent) {
            return $this;
        }

        if ($this->parent !== null) {
            $this->parent->remove($this);
        }

        $this->parent = $parent;

        if ($this->parent !== null && !$this->parent->contains($this, false)) {
            $this->parent->add($this);
        }

        return $this;
    }

    /**
     * Checks for child nodes.
     *
     * @return bool
     */
    public function hasChildren()
    {
        return !empty($this->children);
    }

    /**
     * Gets all child nodes.
     *
     * @return Node[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Adds a node.
     *
     * @param  Node $node
     * @return self
     *
     * @throws \InvalidArgumentException
     */
    public function add(Node $node)
    {
         $this->children[$node->hashCode()] = $node->setParent($this);

        return $this;
    }

    /**
     * Add an array of nodes.
     *
     * @param  Node[]  $nodes
     * @return self
     */
    public function addAll(array $nodes)
    {
        foreach ($nodes as $node) {
            $this->add($node);
        }

        return $this;
    }

    /**
     * Removes a node.
     *
     * @param  Node|string $node
     * @return bool
     */
    public function remove($node)
    {
        $hash = $node instanceof Node ? $node->hashCode() : (string) $node;

        if ($node = $this->find($hash)) {

            unset($this->children[$hash]);
            $node->setParent(null);

            return true;
        }

        return false;
    }

    /**
     * Removes all nodes or an given array of nodes.
     *
     * @param  (Node|string)[] $nodes
     * @return bool
     */
    public function removeAll(array $nodes = [])
    {
        if (empty($nodes)) {

            foreach ($this->children as $child) {
                $child->setParent(null);
            }

            $this->children = [];

            return true;
        }

        $bool = false;

        foreach ($nodes as $node) {
            if ($this->remove($node)) {
                $bool = true;
            }
        }

        return $bool;
    }

    /**
     * Find a node by its hashcode.
     *
     * @param  string $hash
     * @param  bool   $recursive
     * @return Node|null
     */
    public function find($hash, $recursive = true)
    {
        $node = isset($this->children[$hash]) ? $this->children[$hash] : null;

        if (!$node && $recursive) {
            foreach(new \RecursiveIteratorIterator($this, \RecursiveIteratorIterator::SELF_FIRST) as $n) {
                if ($n->hashCode() === $hash) {
                    return $n;
                }
            }
        }

        return $node;
    }

    /**
     * Checks if the tree contains the given node.
     *
     * @param  Node|string $node
     * @param  bool        $recursive
     * @return bool
     */
    public function contains($node, $recursive = true)
    {
        return $this->find(($node instanceof Node ? $node->hashCode() : (string) $node), $recursive) !== null;
    }

    /**
     * Gets the nodes depth.
     *
     * @return int
     */
    public function getDepth()
    {
        if ($this->parent === null) {
            return 0;
        }

        return $this->parent->getDepth() + 1;
    }

    /**
     * Returns a hashcode as unique identifier for a node.
     *
     * @return string
     */
    public function hashCode()
    {
        return spl_object_hash($this);
    }

    /**
     * Gets an iterator for iterating over the tree nodes.
     *
     * @return RecursiveIterator
     */
    public function getIterator()
    {
        return new RecursiveIterator($this);
    }

    /**
     * Returns the number of children.
     *
     * @return int
     */
    public function count()
    {
        return count($this->children);
    }
}
