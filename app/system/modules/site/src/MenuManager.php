<?php

namespace Pagekit\Site;

use Pagekit\Application as App;
use Pagekit\Config\Config;
use Pagekit\Site\Model\Node;

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
     * Gets a node tree.
     *
     * @param  string $menu
     * @param  array  $parameters
     * @return Node|null
     */
    public static function getTree($menu, $parameters = [])
    {
        $parameters = array_replace([
            'start_level' => 1,
            'depth' => PHP_INT_MAX,
            'mode' => 'all'
        ], $parameters);

        $user       = App::user();
        $startLevel = (int) $parameters['start_level'] ?: 1;
        $maxDepth   = $startLevel + ($parameters['depth'] ?: PHP_INT_MAX);

        $nodes               = Node::where(['menu' => $menu, 'status' => 1])->orderBy('priority')->get();
        $nodes[0]            = new Node();
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

        $segments = explode('/', $path);
        $rootPath = count($segments) > $startLevel ? implode('/', array_slice($segments, 0, $startLevel + 1)) : '';

        foreach ($nodes as $node) {

            $depth  = substr_count($node->path, '/');
            $parent = isset($nodes[$node->parent_id]) ? $nodes[$node->parent_id] : null;

            $node->set('active', !$node->path || 0 === strpos($path, $node->path));

            if ($depth >= $maxDepth
                || !$node->hasAccess($user)
                || $node->get('menu_hide')
                || !($parameters['mode'] == 'all'
                    || $node->get('active')
                    || $rootPath && 0 === strpos($node->path, $rootPath)
                    || $depth == $startLevel)
            ) {
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

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return $this->all();
    }
}
