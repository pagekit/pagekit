<?php

namespace Pagekit\View\Helper;

use Pagekit\Application as App;
use Pagekit\Site\Model\Node;

class MenuHelper extends Helper
{
    /**
     * Set shortcut.
     *
     * @see render()
     */
    public function __invoke($name, $view = null, array $parameters = [])
    {
        return $this->render($name, $view, $parameters);
    }

    /**
     * Checks if the menu exists.
     *
     * @param  string $name
     * @return bool
     */
    public function exists($name)
    {
        return (bool) $this->getMenu($name);
    }

    /**
     * Renders a menu.
     *
     * @param  string       $name
     * @param  array|string $view
     * @param  array        $parameters
     * @return string
     */
    public function render($name, $view = null, array $parameters = [])
    {
        if (is_array($view)) {
            $parameters = $view;
            $view = false;
        }

        if (!$menu = $this->getMenu($name)) {
            return '';
        }

        $parameters['root'] = Node::getTree($menu, $parameters);

        return $this->view->render($view ?: 'system/site/menu.php', $parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'menu';
    }

    protected function getMenu($name)
    {
        static $menus;

        if (null === $menus) {
            $menus = App::theme()->getMenus();
        }

        return isset($menus[$name]) && $menus[$name]['assigned'] ? $menus[$name]['assigned'] : null;
    }
}
