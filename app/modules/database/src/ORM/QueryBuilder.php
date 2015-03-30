<?php

namespace Pagekit\Database\ORM;

class QueryBuilder
{
    /**
     * @var EntityManager
     */
    protected $manager;

    /**
     * @var Metadata
     */
    protected $metadata;

    /**
     * @var \Pagekit\Database\Query\QueryBuilder
     */
    protected $query;

    /**
     * @var array
     */
    protected $relations = [];

    /**
     * Constructor.
     *
     * @param EntityManager $manager
     * @param Metadata      $metadata
     */
    public function __construct(EntityManager $manager, Metadata $metadata)
    {
        $this->manager  = $manager;
        $this->metadata = $metadata;
        $this->query    = $manager->getConnection()->createQueryBuilder()->from($metadata->getTable());
    }

    /**
     * Execute the query and get all results.
     *
     * @return array
     */
    public function get()
    {
        if ($entities = $this->manager->hydrateAll($this->query->execute(), $this->metadata)) {
            foreach ($this->getRelations() as $name => $query) {
                $this->manager->related($entities, $name, $query);
            }
        }

        return $entities;
    }

    /**
     * Execute the query and get the first result.
     *
     * @return mixed
     */
    public function first()
    {
        if ($entity = $this->manager->hydrateOne($this->query->limit(1)->execute(), $this->metadata)) {

            foreach ($this->getRelations() as $name => $query) {
                $this->manager->related($entity, $name, $query);
            }

            return $entity;
        }

        return null;
    }

    /**
     * Set the relations that will be eager loaded.
     *
     * @param  mixed $related
     * @return self
     */
    public function related($related)
    {
        if (is_string($related)) {
            $related = func_get_args();
        }

        $relations = [];

        foreach ($related as $name => $constraints) {

            // no constrains
            if (is_numeric($name)) {
                list($name, $constraints) = [$constraints, function () {}];
            }

            // is nested ?
            if (strpos($name, '.') !== false) {

                $progress = [];

                foreach (explode('.', $name) as $part) {

                    $progress[] = $part;

                    if (!isset($relations[$last = implode('.', $progress)])) {
                        $relations[$last] = function () {};
                    }
                }
            }

            $relations[$name] = $constraints;
        }

        $this->relations = array_merge($this->relations, $relations);

        return $this;
    }

    /**
     * Gets all relations of the query.
     *
     * @return array
     */
    public function getRelations()
    {
        $relations = [];

        foreach ($this->relations as $name => $constraints) {
            if (strpos($name, '.') === false) {

                $mapping = $this->metadata->getRelationMapping($name);
                $query   = call_user_func("{$mapping['targetEntity']}::query");

                if ($nested = $this->getNestedRelations($name)) {
                    $query->related($nested);
                }

                call_user_func($constraints, $query);

                $relations[$name] = $query;
            }
        }

        return $relations;
    }

    /**
     * Gets all nested relations of the query.
     *
     * @param  string $relation
     * @return array
     */
    public function getNestedRelations($relation)
    {
        $nested = [];
        $prefix = $relation.'.';

        foreach ($this->relations as $name => $constraints) {
            if ($prefix == substr($name, 0, strlen($prefix))) {
                $nested[substr($name, strlen($prefix))] = $constraints;
            }
        }

        return $nested;
    }

    /**
     * Proxy method call to query builder.
     *
     * @param  string $method
     * @param  array  $args
     * @throws \BadMethodCallException
     * @return mixed
     */
    public function __call($method, $args)
    {
        if (!method_exists($this->query, $method)) {
            throw new \BadMethodCallException(sprintf('Undefined method call "%s::%s"', get_class($this), $method));
        }

        $result = call_user_func_array([$this->query, $method], $args);

        return $result === $this->query ? $this : $result;
    }
}
