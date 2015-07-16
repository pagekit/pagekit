<?php

namespace Pagekit\Site;

use Pagekit\Application as App;

class MenuManager implements \JsonSerializable
{
    protected $menus = [];

    /**
     * Get shortcut.
     *
     * @see get()
     */
    public function __invoke($id)
    {
        return $this->get($id);
    }

    /**
     * Gets menu by id.
     *
     * @param  string $id
     * @return array
     */
    public function get($id)
    {
        $menus = $this->all();

        return isset($menus[$id]) ? $menus[$id] : null;
    }

    /**
     * Gets menus.
     *
     * @return array
     */
    public function all()
    {
        uasort($this->menus, function ($a, $b) {
            return strcmp($a['label'], $b['label']);
        });

        return $this->menus + [['id' => '', 'label' =>'Not Linked', 'fixed' => true]];
    }

    /**
     * Registers a menu.
     *
     * @param string $id
     * @param string $label
     * @param array  $options
     */
    public function register($id, $label, array $options = [])
    {
        $this->menus[$id] = array_merge($options, compact('id', 'label'));
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return $this->all();
    }
}
