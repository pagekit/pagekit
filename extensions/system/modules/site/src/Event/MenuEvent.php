<?php

namespace Pagekit\Site\Event;

use Pagekit\Application\Event;

class MenuEvent extends Event implements \IteratorAggregate
{
    /**
     * Registers a menu.
     *
     * @param string $id
     * @param string $label
     * @param array  $options
     */
    public function register($id, $label, array $options = [])
    {
        $this->parameters[$id] = array_merge($options, compact('id', 'label'));
    }

    /**
     * Get list of menus.
     *
     * @return array
     */
    public function getMenus()
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
