<?php

namespace Pagekit\Menu\Model;

class Menu implements MenuInterface
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var array
     */
    protected $items;

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function addItem(ItemInterface $item)
    {
        return $this->items[$item->getId()] = $item;
    }

    /**
     * {@inheritdoc}
     */
    public function getItem($id)
    {
        return isset($this->items[$id]) ? $this->items[$id] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function setItems(array $items = []) {
        $this->items = $items;
    }

    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        return (array) $this->items;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->getItems());
    }
}
