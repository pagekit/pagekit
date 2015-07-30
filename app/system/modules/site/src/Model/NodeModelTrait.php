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

        $nodes               = self::where(['menu' => $menu, 'status' => 1])->orderBy('priority')->get();
        $nodes[0]            = new static();
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
     * @Saving
     */
    public static function saving($event, Node $node)
    {
        $db = self::getConnection();

        $i  = 2;
        $id = $node->id;

        if (!$node->slug) {
            $node->slug = $node->title;
        }

        while (self::where(['slug = ?', 'parent_id= ?'], [$node->slug, $node->parent_id])->where(function ($query) use ($id) {
            if ($id) $query->where('id <> ?', [$id]);
        })->first()) {
            $node->slug = preg_replace('/-\d+$/', '', $node->slug).'-'.$i++;
        }

        // Update own path
        $path = '/'.$node->slug;
        if ($node->parent_id && $parent = self::find($node->parent_id) and $parent->menu === $node->menu) {
            $path = $parent->path.$path;
        } else {
            // set Parent to 0, if old parent is not found
            $node->parent_id = 0;
        }
        $node->path = $path;

        if ($id) {
            // Update children's paths
            foreach (self::where(['parent_id' => $id])->get() as $child) {
                if (0 !== strpos($child->path, $node->path.'/') || $child->menu !== $node->menu) {
                    $child->menu = $node->menu;
                    $child->save();
                }
            }
        } else {
            // Set priority
            $node->priority = 1 + $db->createQueryBuilder()
                    ->select($db->getDatabasePlatform()->getMaxExpression('priority'))
                    ->from('@system_node')
                    ->where(['parent_id' => $node->parent_id])
                    ->execute()
                    ->fetchColumn();
        }
    }

    /**
     * @Deleting
     */
    public static function deleting($event, Node $node)
    {
        // Update children's parents
        foreach (self::where('parent_id = ?', [$node->id])->get() as $child) {
            $child->parent_id = $node->parent_id;
            $child->save();
        }
    }
}
