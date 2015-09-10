<?php

namespace Pagekit\System\Model;

trait NodeTrait
{
    /**
     * @var NodeInterface|null
     */
    protected $parent;

    /**
     * @var NodeInterface[]
     */
    protected $children = [];

    /**
     * @return NodeInterface|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * {@inheritdoc}
     */
    public function setParent(NodeInterface $parent = null)
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
     * {@inheritdoc}
     */
    public function hasChildren()
    {
        return !empty($this->children);
    }

    /**
     * {@inheritdoc}
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * {@inheritdoc}
     */
    public function add(NodeInterface $node)
    {
         $this->children[$node->hashCode()] = $node->setParent($this);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addAll(array $nodes)
    {
        foreach ($nodes as $node) {
            $this->add($node);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($node)
    {
        $hash = $node instanceof NodeInterface ? $node->hashCode() : (string) $node;

        if ($node = $this->findChild($hash)) {

            unset($this->children[$hash]);
            $node->setParent(null);

            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function findChild($hash, $recursive = true)
    {
        $node = isset($this->children[$hash]) ? $this->children[$hash] : null;

        if (!$node && $recursive) {
            foreach($this->getChildren() as $n) {
                if ($child = $n->findChild($hash, $recursive)) {
                    return $child;
                }
            }
        }

        return $node;
    }

    /**
     * {@inheritdoc}
     */
    public function contains($node, $recursive = true)
    {
        return $this->findChild(($node instanceof NodeInterface ? $node->hashCode() : (string) $node), $recursive) !== null;
    }

    /**
     * {@inheritdoc}
     */
    public function getDepth()
    {
        if ($this->parent === null) {
            return 0;
        }

        return $this->parent->getDepth() + 1;
    }

    /**
     * {@inheritdoc}
     */
    public function hashCode()
    {
        return spl_object_hash($this);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->children);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->children);
    }
}
