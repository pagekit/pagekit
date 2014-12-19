<?php

namespace Pagekit\Tree\Event;

use Pagekit\Tree\Entity\Node;
use Symfony\Component\EventDispatcher\Event;

class NodeEditEvent extends Event
{
    protected $node;
    protected $config;

    public function __construct(Node $node, array $config)
    {
        $this->node = $node;
        $this->config = $config;
    }

    /**
     * @return Node
     */
    public function getNode()
    {
        return $this->node;
    }

    /**
     * @param Node $node
     */
    public function setNode($node)
    {
        $this->node = $node;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function setConfig($key, $value)
    {
        $this->config[$key] = $value;
    }
}
