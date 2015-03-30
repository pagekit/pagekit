<?php

namespace Pagekit\Database\ORM\Relation;

use Pagekit\Database\ORM\QueryBuilder;

class HasOne extends Relation
{
    /**
     * @var string
     */
    protected $belongsTo;

    /**
     * {@inheritdoc}
     */
    public function __construct($manager, $metadata, $mapping)
    {
        parent::__construct($manager, $metadata, $mapping);

        $this->keyFrom = $mapping['keyFrom'] ? $mapping['keyFrom'] : $metadata->getIdentifier();
        $this->keyTo   = $mapping['keyTo'];

        foreach ($this->targetMetadata->getRelationMappings() as $mapping) {
            if ($mapping['type'] == 'BelongsTo' && $mapping['targetEntity'] == $this->metadata->getClass()) {
                $this->belongsTo = $mapping['name'];
                break;
            }
        }
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
        $this->mapBelongsTo($entities);
        $this->resolveRelations($query, $targets);
    }

    protected function mapBelongsTo($entities)
    {
        if ($this->belongsTo) {
            foreach ($entities as $entity) {
                if ($target = $this->metadata->getValue($entity, $this->name)) {
                    $this->targetMetadata->setValue($target, $this->belongsTo, $entity, true);
                }
            }
        }
    }
}
