<?php

namespace Pagekit\Site\Model;

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
}
