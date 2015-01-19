<?php

namespace Pagekit\Site\Event;

use Pagekit\Site\Entity\Node;
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
     * @param array $config
     */
    public function addConfig(array $config)
    {
        $this->config = array_merge_recursive($this->config, $config);
    }
}
