<?php

namespace Pagekit\Database\ORM\Relation;

use Pagekit\Database\ORM\QueryBuilder;

class HasMany extends HasOne
{
    /**
     * @var array
     */
    protected $orderBy;

    /**
     * {@inheritdoc}
     */
    public function __construct($manager, $metadata, $mapping)
    {
        parent::__construct($manager, $metadata, $mapping);

        $this->orderBy = $mapping['orderBy'];
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(array $entities, QueryBuilder $query)
    {
        $this->initRelation($entities, []);

        if (!$keys = $this->getKeys($entities)) {
            return;
        }

        if ($this->orderBy) {
            foreach ($this->orderBy as $column => $order) {
                $query->orderBy($column, $order);
            }
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
                foreach ($this->metadata->getValue($entity, $this->name) as $target) {
                    $this->targetMetadata->setValue($target, $this->belongsTo, $entity, true);
                }
            }
        }
    }
}
