<?php

namespace Pagekit\View\Helper;

use Pagekit\Application;
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
     * Checks if the position exists.
     *
     * @param  string $name
     * @return bool
     */
    public function exists($name)
    {
        return true;
    }

    /**
     * Renders a position.
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

        $parameters['root'] = Node::getTree($name);

        return $this->view->render($view ?: 'menu', $parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'menu';
    }
}
