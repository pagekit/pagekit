<?php

namespace Pagekit\System\Link;

class LinkManager implements \IteratorAggregate
{
    /**
     * @var LinkInterface[]
     */
    private $links = array();

    /**
     * Check if a link type is registered.
     *
     * @param  string $route
     * @return boolean
     */
    public function has($route)
    {
        return isset($this->links[$route]);
    }

    /**
     * Returns a registered link type.
     *
     * @param  string $route
     * @return LinkInterface
     */
    public function get($route)
    {
        return $this->has($route) ? $this->links[$route] : null;
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

        $this->links[$link->getRoute()] = $link;

        uasort($this->links, function($typeA, $typeB) { return strcmp($typeA->getLabel(), $typeB->getLabel()); });
    }

    /**
     * Unregisters a link type.
     *
     * @param  string  $link
     */
    public function unregister($link)
    {
        unset($this->links[$link]);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->links);
    }
}
