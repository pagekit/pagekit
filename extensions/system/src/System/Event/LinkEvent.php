<?php

namespace Pagekit\System\Event;

use Pagekit\System\Link\LinkInterface;
use Symfony\Component\EventDispatcher\Event;

class LinkEvent extends Event implements \IteratorAggregate
{
    /**
     * @var string
     */
    protected $context;

    /**
     * @var LinkInterface[]
     */
    protected $links = [];

    public function __construct($context = '')
    {
        $this->context = $context;
    }

    /**
     * Gets the event context.
     *
     * @return string
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Register a link type.
     *
     * @param  string|LinkInterface $link
     * @throws \RuntimeException
     */
    public function register($link)
    {
        if (!is_subclass_of($link, 'Pagekit\System\Link\LinkInterface')) {
            throw new \RuntimeException(sprintf('The link type %s does not implement LinkInterface', $link));
        }

        if (is_string($link)) {
            $link = new $link;
        }

        $this->links[$link->getId()] = $link;

        uasort($this->links, function(LinkInterface $typeA, LinkInterface $typeB) { return strcmp($typeA->getLabel(), $typeB->getLabel()); });
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->links);
    }
}
