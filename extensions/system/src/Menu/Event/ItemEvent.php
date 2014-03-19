<?php

namespace Pagekit\Menu\Event;

use Pagekit\Framework\Event\Event;
use Pagekit\Menu\Model\ItemInterface;

class ItemEvent extends Event
{
    /**
     * @var ItemInterface
     */
    protected $item;

    /**
     * Constructs an event.
     *
     * @param ItemInterface $item
     */
    public function __construct(ItemInterface $item)
    {
        $this->item = $item;
    }

    /**
     * Returns the menu item for this event.
     *
     * @return ItemInterface
     */
    public function getItem()
    {
        return $this->item;
    }
}