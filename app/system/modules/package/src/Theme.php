<?php

namespace Pagekit\System;

use Pagekit\Application as App;
use Pagekit\Module\Module;

class Theme extends Module implements \JsonSerializable
{
    /**
     * @var string
     */
    protected $layout = '/views/template.php';

    /**
     * Gets the layout path.
     *
     * @return string
     */
    public function getLayout()
    {
        return $this->path.$this->layout;
    }

    /**
     * Gets the theme menus.
     *
     * @return array
     */
    public function getMenus()
    {
        $menus = [];

        foreach ($this->get('menus', []) as $name => $label) {
            $menus[$name] = [
                'name' => $name,
                'label' => $label,
                'assigned' => $this->config("menus.$name", null)
            ];
        }

        return $menus;
    }

    /**
     * Assigns menus to a theme menu.
     *
     * @param array   $positions
     * @param integer $id
     */
    public function assignMenu(array $positions, $id)
    {
        $menus = $this->config('menus', []);

        foreach ($this->getMenus() as $name => $menu) {
            if (in_array($name, $positions)) {
                $menus[$name] = $id;
            } elseif ($menu['assigned'] == $id) {
                $menus[$name] = null;
            }
        }

        $this->config['menus'] = $menus;
        $this->save();
    }

    /**
     * Gets the theme positions.
     *
     * @return array
     */
    public function getPositions()
    {
        $positions = [];

        foreach ($this->get('positions', []) as $name => $label) {
            $positions[$name] = [
                'name' => $name,
                'label' => $label,
                'assigned' => $this->config("positions.$name", [])
            ];
        }

        return $positions;
    }

    /**
     * Finds a theme position by widget id.
     *
     * @param  integer $id
     * @return string
     */
    public function findPosition($id)
    {
        foreach ($this->config('positions', []) as $name => $assigned) {
            if (in_array($id, $assigned)) {
                return $name;
            }
        }

        return '';
    }

    /**
     * Assigns widgets to a theme position.
     *
     * @param string        $position
     * @param array|integer $id
     */
    public function assignPosition($position, $id)
    {
        $positions = $this->config('positions', []);

        if (!is_array($id) && $position === $this->findPosition($id)) {
            return;
        }

        foreach ($positions as $name => $assigned) {
            $positions[$name] = array_values(array_diff($assigned, (array) $id));
        }

        if (is_array($id)) {
            $positions[$position] = array_values(array_unique($id));
        } else {
            $positions[$position][] = $id;
        }

        $this->config['positions'] = $positions;
        $this->save();
    }

    /**
     * Saves the theme config.
     *
     * @return array
     */
    public function save()
    {
        App::config('theme')->set($this->name, $this->config(['menus', 'positions']));
    }

    /**
     * Implements JsonSerializable interface.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'name' => $this->name,
            'config' => $this->config,
            'menus' => array_values($this->getMenus()),
            'positions' => array_values($this->getPositions())
        ];
    }
}
