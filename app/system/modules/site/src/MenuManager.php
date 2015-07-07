<?php

namespace Pagekit\Site;

use Pagekit\Application as App;
use Pagekit\Site\Model\Node;
use Pagekit\System\Model\NodeInterface;

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
     * Renders a menu.
     *
     * @param string $menu
     * @param array  $options
     * @return NodeInterface|null
     */
    public function render($menu, $options = [])
    {
        $options = array_replace([
            'start_level' => 1,
            'depth' => PHP_INT_MAX,
            'mode' => 'all'
        ], $options);


        $user       = App::user();
        $startLevel = (int) $options['start_level'] ?: 1;
        $maxDepth   = $startLevel + ($options['depth'] ?: PHP_INT_MAX);

        $path       = App::node()->getPath();
        $segments   = explode('/', $path);
        $rootPath   = count($segments) > $startLevel ? implode('/', array_slice($segments, 0, $startLevel + 1)) : '';

        $nodes      = Node::where(['menu' => $menu, 'status' => 1])->orderBy('priority')->get();
        $nodes[0]   = new Node();
        $nodes[0]->setParentId(null);

        foreach ($nodes as $node) {

            $depth  = substr_count($node->getPath(), '/');
            $parent = isset($nodes[$node->getParentId()]) ? $nodes[$node->getParentId()] : null;

            $node->set('active', !$node->getPath() || 0 === strpos($path, $node->getPath()));

            if ($depth >= $maxDepth
                || !$node->hasAccess($user)
                || !($options['mode'] == 'all'
                    || $node->get('active')
                    || $rootPath && 0 === strpos($node->getPath(), $rootPath)
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
