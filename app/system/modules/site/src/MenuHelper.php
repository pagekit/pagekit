<?php

namespace Pagekit\Site;

use Pagekit\Site\Model\Node;
use Pagekit\View\Helper\Helper;

class MenuHelper extends Helper
{
    protected $menus;

    public function __construct(MenuManager $menus)
    {
        $this->menus = $menus;
    }

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
        return (bool) $this->menus->find($name);
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

        if (!$menu = $this->menus->find($name)) {
            return '';
        }

        $parameters['root'] = $this->menus->getTree($menu, $parameters);

        return $this->view->render($view ?: 'system/site/menu.php', $parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'menu';
    }
}
