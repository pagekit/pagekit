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

        $nodes      = self::where(['menu' => $menu, 'status' => 1])->orderBy('priority')->get();
        $nodes[0]   = new static();
        $nodes[0]->setParentId(null);

        $node = App::node();
        $path = $node->getPath();
        
        if (!isset($nodes[$node->getId()])) {
            foreach($nodes as $node) {
                if ($node->getUrl('base') === $path) {
                    $path = $node->getPath();
                    break;
                }
            }
        }

        $segments   = explode('/', $path);
        $rootPath   = count($segments) > $startLevel ? implode('/', array_slice($segments, 0, $startLevel + 1)) : '';

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

    /**
     * @Saving
     */
    public static function saving($event, NodeInterface $node)
    {
        $db = self::getConnection();

        $i  = 2;
        $id = $node->getId();

        if (!$node->getSlug()) {
            $node->setSlug($node->getTitle());
        }

        while (self::where(['slug = ?', 'parent_id= ?'], [$node->getSlug(), $node->getParentId()])->where(function ($query) use ($id) {
            if ($id) $query->where('id <> ?', [$id]);
        })->first()) {
            $node->setSlug(preg_replace('/-\d+$/', '', $node->getSlug()).'-'.$i++);
        }

        // Update own path
        $path = '/'.$node->getSlug();
        if ($node->getParentId() && $parent = self::find($node->getParentId()) and $parent->getMenu() === $node->getMenu()) {
            $path = $parent->getPath().$path;
        } else {
            // set Parent to 0, if old parent is not found
            $node->setParentId(0);
        }
        $node->setPath($path);

        if ($id) {
            // Update children's paths
            foreach (self::where(['parent_id' => $id])->get() as $child) {
                if (0 !== strpos($child->getPath(), $node->getPath().'/') || $child->getMenu() !== $node->getMenu()) {
                    $child->setMenu($node->getMenu());
                    $child->save();
                }
            }
        } else {
            // Set priority
            $node->setPriority(
                1 + $db->createQueryBuilder()
                    ->select($db->getDatabasePlatform()->getMaxExpression('priority'))
                    ->from('@system_node')
                    ->where(['parent_id' => $node->getParentId()])
                    ->execute()
                    ->fetchColumn()
            );
        }
    }

    /**
     * @Deleting
     */
    public static function deleting($event, NodeInterface $node)
    {
        // Update children's parents
        foreach (self::where('parent_id = ?', [$node->getId()])->get() as $child) {
            $child->setParentId($node->getParentId());
            $child->save();
        }
    }
}
