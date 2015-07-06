<?php

namespace Pagekit\System;

use Pagekit\Application as App;
use Pagekit\Module\Module;

class Theme extends Module implements \JsonSerializable
{
    /**
     * @var string
     */
    protected $layout = '/templates/template.php';

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
        return $this->get('menus');
    }

    /**
     * Gets the theme positions.
     *
     * @return array
     */
    public function getPositions()
    {
        return $this->get('positions');
    }

    /**
     * Implements JsonSerializable interface.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->get(['name', 'menus', 'positions']);
    }
}
