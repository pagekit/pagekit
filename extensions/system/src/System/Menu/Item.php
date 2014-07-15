<?php

namespace Pagekit\System\Menu;

use Pagekit\Menu\Model\Item as BaseItem;

class Item extends BaseItem
{
    /**
     * @var string
     */
    protected $icon;

    /**
     * @var string
     */
    protected $access;

    /**
     * Constructor.
     *
     * @param array $properties
     */
    public function __construct($properties = [])
    {
        foreach ($properties as $property => $value) {
            if (property_exists($this, $property)) {
                $this->$property = $value;
            } else {
                $this->setAttribute($property, $value);
            }
        }
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @param string
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;
    }

    /**
     * @return string
     */
    public function getAccess()
    {
        return $this->access;
    }

    /**
     * @param string
     */
    public function setAccess($access)
    {
        $this->access = (string) $access;
    }
}
