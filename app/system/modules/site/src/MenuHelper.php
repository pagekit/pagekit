<?php

namespace Pagekit\Site;

use Pagekit\Application as App;
use Pagekit\Site\Model\Node;
use Pagekit\View\Helper\Helper;

class MenuHelper extends Helper
{
    /**
     * @var MenuManager
     */
    protected $menus;

    /**
     * @param MenuManager $menus
     */
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
        if (!$name = $this->menus->find($name)) {
            return '';
        }

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

        if (!$root = $this->getRoot($name, $parameters)) {
            return '';
        }

        return $this->view->render($view ?: 'system/site/menu.php', array_replace($parameters, compact('root')));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'menu';
    }

    /**
     * @param  string $menu
     * @param  array  $parameters
     * @return Node|null
     */
    public function getRoot($menu, $parameters = [])
    {
        $parameters = array_replace([
            'start_level' => 1,
            'depth' => PHP_INT_MAX,
            'mode' => 'all'
        ], $parameters);

        $user = App::user();
        $startLevel = (int) $parameters['start_level'] ?: 1;
        $maxDepth = $startLevel + ($parameters['depth'] ?: PHP_INT_MAX);

        $nodes = Node::findByMenu($menu, true);
        $nodes[0] = new Node(['path' => '/']);
        $nodes[0]->status = 1;
        $nodes[0]->parent_id = null;

        $node = App::node();
        $path = $node->path;

        if (!isset($nodes[$node->id])) {
            foreach ($nodes as $node) {
                if ($node->getUrl('base') === $path) {
                    $path = $node->path;
                    break;
                }
            }
        }

        $path .= '/';

        $segments = explode('/', $path);
        $rootPath = count($segments) > $startLevel ? implode('/', array_slice($segments, 0, $startLevel + 1)).'/' : '/';

        foreach ($nodes as $node) {

            $depth = substr_count($node->path, '/');
            $parent = isset($nodes[$node->parent_id]) ? $nodes[$node->parent_id] : null;

            $node->set('active', 0 === strpos($path, $node->path.'/'));

            if ($node->status !== 1
                || $depth >= $maxDepth
                || !$node->hasAccess($user)
                || $node->get('menu_hide')
                || !($parameters['mode'] == 'all'
                    || $node->get('active')
                    || 0 === strpos($node->path.'/', $rootPath)
                    || $depth == $startLevel)
            ) {
                $node->setParent();
                continue;
            }

            $node->setParent($parent);

            if ($node->get('active') && $depth == $startLevel - 1) {
                $root = $node;
            }

        }

        if (!isset($root)) {
            return null;
        }

        $root->setParent();

        return $root;
    }
}
