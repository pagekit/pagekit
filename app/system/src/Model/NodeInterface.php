<?php

namespace Pagekit\System\Model;

interface NodeInterface extends \IteratorAggregate, \Countable
{
    /**
     * @return NodeInterface|null
     */
    public function getParent();

    /**
     * Sets the parent node.
     *
     * @param  NodeInterface|null $parent
     * @return self
     *
     * @throws \InvalidArgumentException
     */
    public function setParent(NodeInterface $parent = null);

    /**
     * Checks for child nodes.
     *
     * @return bool
     */
    public function hasChildren();

    /**
     * Gets all child nodes.
     *
     * @return NodeInterface[]
     */
    public function getChildren();

    /**
     * Adds a node.
     *
     * @param  NodeInterface $node
     * @return self
     *
     * @throws \InvalidArgumentException
     */
    public function add(NodeInterface $node);

    /**
     * Add an array of nodes.
     *
     * @param  NodeInterface[]  $nodes
     * @return self
     */
    public function addAll(array $nodes);

    /**
     * Removes a node.
     *
     * @param  NodeInterface|string $node
     * @return bool
     */
    public function remove($node);

    /**
     * Removes all nodes or an given array of nodes.
     *
     * @param  (NodeInterface|string)[] $nodes
     * @return bool
     */
    public function removeAll(array $nodes = []);

    /**
     * Find a node by its hashcode.
     *
     * @param  string $hash
     * @param  bool   $recursive
     * @return NodeInterface|null
     */
    public function findChild($hash, $recursive = true);


    /**
     * Checks if the tree contains the given node.
     *
     * @param  NodeInterface|string $node
     * @param  bool        $recursive
     * @return bool
     */
    public function contains($node, $recursive = true);

    /**
     * Gets the nodes depth.
     *
     * @return int
     */
    public function getDepth();

    /**
     * Returns a hashcode as unique identifier for a node.
     *
     * @return string
     */
    public function hashCode();
}
