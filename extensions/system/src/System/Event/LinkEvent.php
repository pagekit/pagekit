<?php

namespace Pagekit\System\Event;

use Pagekit\Framework\Event\Event;
use Pagekit\System\Link\LinkInterface;

class LinkEvent extends Event implements \IteratorAggregate
{
    /**
     * @var string
     */
    protected $context;

    /**
     * @var LinkInterface[]
     */
    protected $parameters;

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

        $this->parameters[$link->getId()] = $link;

        uasort($this->parameters, function(LinkInterface $typeA, LinkInterface $typeB) { return strcmp($typeA->getLabel(), $typeB->getLabel()); });
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->parameters);
    }
}
