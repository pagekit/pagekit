<?php

namespace Pagekit\Database\ORM\Relation;

use Pagekit\Database\ORM\QueryBuilder;

class BelongsTo extends Relation
{
    /**
     * {@inheritdoc}
     */
    public function __construct($manager, $metadata, $mapping)
    {
        parent::__construct($manager, $metadata, $mapping);

        $this->keyFrom = $mapping['keyFrom'];
        $this->keyTo   = $mapping['keyTo'] ? $mapping['keyTo'] :  $this->targetMetadata->getIdentifier();
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(array $entities, QueryBuilder $query)
    {
        $this->initRelation($entities);

        if (!$keys = $this->getKeys($entities)) {
            return;
        }

        $targets = $query->whereIn($this->keyTo, $keys)->get();

        $this->map($entities, $targets);
        $this->resolveRelations($query, $targets);
    }
}
