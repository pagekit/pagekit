<?php

namespace Pagekit\Site\Model;

use Pagekit\Application as App;
use Pagekit\Database\ORM\ModelTrait;

trait NodeModelTrait
{
    use ModelTrait;

    /**
     * Sets parent_id of orphaned nodes to zero.
     *
     * @return int
     */
    public static function fixOrphanedNodes()
    {
        if ($orphaned = self::getConnection()
            ->createQueryBuilder()
            ->from('@system_node n')
            ->leftJoin('@system_node c', 'c.id = n.parent_id AND c.menu = n.menu')
            ->where(['n.parent_id <> 0', 'c.id IS NULL'])
            ->execute('n.id')->fetchAll(\PDO::FETCH_COLUMN)
        ) {
            self::query()
                ->whereIn('id', $orphaned)
                ->update(['parent_id' => 0]);
        }
    }

    /**
     * Gets a node tree.
     *
     * @param  string $menu
     * @param  array  $parameters
     * @return NodeInterface|null
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

        $path       = App::node()->getPath();
        $segments   = explode('/', $path);
        $rootPath   = count($segments) > $startLevel ? implode('/', array_slice($segments, 0, $startLevel + 1)) : '';

        $nodes      = self::where(['menu' => $menu, 'status' => 1])->orderBy('priority')->get();
        $nodes[0]   = new static();
        $nodes[0]->setParentId(null);

        foreach ($nodes as $node) {

            $depth  = substr_count($node->getPath(), '/');
            $parent = isset($nodes[$node->getParentId()]) ? $nodes[$node->getParentId()] : null;

            $node->set('active', !$node->getPath() || 0 === strpos($path, $node->getPath()));

            if ($depth >= $maxDepth
                || !$node->hasAccess($user)
                || $node->get('menu_hide')
                || !($parameters['mode'] == 'all'
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
}
