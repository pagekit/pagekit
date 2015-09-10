<?php

namespace Pagekit\Site;

use Pagekit\Application as App;
use Pagekit\Config\Config;

class MenuManager implements \JsonSerializable
{
    protected $positions = [];
    protected $menus;
    protected $config;

    public function __construct(Config $config, array $menus = [])
    {
        $this->config = $config;
        $this->menus  = $menus;
    }

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
        $menus = $this->menus;

        foreach ($menus as $id => &$menu) {
            $menu['positions'] = array_keys($this->config->get('_menus', []), $id);
        }

        uasort($menus, function ($a, $b) {
            return strcmp($a['label'], $b['label']);
        });

        return $menus + ['' => ['id' => '', 'label' => 'Not Linked', 'fixed' => true]];
    }

    /**
     * Registers a menu position.
     *
     * @param string $name
     * @param string $label
     */
    public function register($name, $label)
    {
        $this->positions[$name] = compact('name', 'label');
    }

    /**
     * Gets the menu positions.
     *
     * @return array
     */
    public function getPositions()
    {
        return $this->positions;
    }

    /**
     * Finds an assigned menu by position.
     *
     * @param  string $position
     * @return string
     */
    public function find($position)
    {
        return $this->config->get("_menus.{$position}");
    }

    /**
     * Assigns a menu to menu positions.
     *
     * @param string $id
     * @param array  $positions
     */
    public function assign($id, array $positions)
    {
        $menus = $this->config->get('_menus', []);
        $menus = array_diff($menus, [$id]);

        foreach ($positions as $position) {
            $menus[$position] = $id;
        }

        $this->config->set('_menus', $menus);
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return $this->all();
    }
}
