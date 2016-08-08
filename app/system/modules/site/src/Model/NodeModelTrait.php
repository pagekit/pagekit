<?php

namespace Pagekit\Site\Model;

use Pagekit\Application as App;
use Pagekit\Database\ORM\ModelTrait;

trait NodeModelTrait
{
    use ModelTrait {
        find as modelFind;
    }

    protected static $nodes;

    /**
     * Retrieves an entity by its identifier.
     *
     * @param  mixed $id
     * @param  bool  $cached
     * @return static
     */
    public static function find($id, $cached = false)
    {
        if (!$cached || !isset(self::$nodes[$id])) {
            self::$nodes[$id] = self::modelFind($id);
        }

        return self::$nodes[$id];
    }

    /**
     * Retrieves all entities.
     *
     * @param  bool $cached
     * @return static[]
     */
    public static function findAll($cached = false)
    {
        if (!$cached || null === self::$nodes) {
            self::$nodes = self::query()->orderBy('priority')->get();
        }

        return self::$nodes;
    }

    /**
     * Retrieves all nodes by menu.
     *
     * @return static[]
     */
    public static function findByMenu($menu, $cached = false)
    {
        return array_filter(self::findAll($cached), function ($node) use ($menu) { return $menu == $node->menu; });
    }

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
            return self::query()
                ->whereIn('id', $orphaned)
                ->update(['parent_id' => 0]);
        }

        return 0;
    }

    /**
     * @Saving
     */
    public static function saving($event, Node $node)
    {
        $db = self::getConnection();

        $i = 2;
        $id = $node->id;

        if (!$node->slug) {
            $node->slug = $node->title;
        }

        // A node cannot have itself as a parent
        if ($node->parent_id === $node->id) {
            $node->parent_id = 0;
        }

        // Ensure unique slug
        while (self::where(['slug = ?', 'parent_id= ?'], [$node->slug, $node->parent_id])->where(function ($query) use ($id) {
            if ($id) $query->where('id <> ?', [$id]);
        })->first()) {
            $node->slug = preg_replace('/-\d+$/', '', $node->slug).'-'.$i++;
        }

        // Update own path
        $path = '/'.$node->slug;
        if ($node->parent_id && $parent = Node::find($node->parent_id) and $parent->menu == $node->menu) {
            $path = $parent->path.$path;
        } else {
            // set Parent to 0, if old parent is not found
            $node->parent_id = 0;
        }

        // Update children's paths
        if ($id && $path != $node->path) {
            $db->executeUpdate(
                'UPDATE '.self::getMetadata()->getTable()
                .' SET path = REPLACE ('.$db->getDatabasePlatform()->getConcatExpression($db->quote('//'), 'path').", {$db->quote('//' . $node->path)}, {$db->quote($path)})"
                .' WHERE path LIKE '.$db->quote($node->path.'//%'));
        }

        $node->path = $path;

        // Set priority
        if (!$id) {
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
