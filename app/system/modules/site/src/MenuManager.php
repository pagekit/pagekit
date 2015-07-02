<?php

namespace Pagekit\Site;

class MenuManager implements \JsonSerializable
{
    protected $menus = [];

    /**
     * Get shortcut.
     *
     * @see get()
     */
    public function __invoke($id = null)
    {
        return $this->get($id);
    }

    /**
     * Gets menus.
     *
     * @param  string $id
     * @return array
     */
    public function get($id = null)
    {
        $menus = $this->getMenus();

        if ($id === null) {
            return $menus;
        }

        return isset($menus[$id]) ? $menus[$id] : null;
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
     * Gets menus.
     *
     * @return array
     */
    protected function getMenus()
    {
        uasort($this->menus, function ($a, $b) {
            return strcmp($a['label'], $b['label']);
        });

        return $this->menus + [['id' => '', 'label' =>'Not Linked', 'fixed' => true]];
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return $this->get();
    }
}
