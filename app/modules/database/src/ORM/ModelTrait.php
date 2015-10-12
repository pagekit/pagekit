<?php

namespace Pagekit\Database\ORM;

use Pagekit\Database\Connection;

trait ModelTrait
{
    use PropertyTrait;

    /**
     * Gets the related EntityManager.
     *
     * @return EntityManager
     */
    public static function getManager()
    {
        static $manager;

        if (!$manager) {
            $manager = EntityManager::getInstance();
        }

        return $manager;
    }

    /**
     * @return Connection
     */
    public static function getConnection()
    {
        return static::getManager()->getConnection();
    }

    /**
     * Gets the related Metadata object with mapping information of the class.
     *
     * @return Metadata
     */
    public static function getMetadata()
    {
        return static::getManager()->getMetadata(get_called_class());
    }

    /**
     * Creates a new instance of this model.
     *
     * @param  array $data
     * @return static
     */
    public static function create($data = [])
    {
        return static::getManager()->load(self::getMetadata(), $data);
    }

    /**
     * Creates a new QueryBuilder instance.
     *
     * @return QueryBuilder
     */
    public static function query()
    {
        return new QueryBuilder(static::getManager(), static::getMetadata());
    }

    /**
     * Creates a new QueryBuilder instance and set the WHERE condition.
     *
     * @param  mixed $condition
     * @param  array $params
     * @return QueryBuilder
     */
    public static function where($condition, array $params = [])
    {
        return static::query()->where($condition, $params);
    }

    /**
     * Retrieves an entity by its identifier.
     *
     * @param  mixed $id
     * @return static
     */
    public static function find($id)
    {
        return static::where([static::getMetadata()->getIdentifier() => $id])->first();
    }

    /**
     * Retrieves all entities.
     *
     * @return static[]
     */
    public static function findAll()
    {
        return static::query()->get();
    }

    /**
     * Saves the entity.
     *
     * @param array $data
     */
    public function save(array $data = [])
    {
        static::getManager()->save($this, $data);
    }

    /**
     * Deletes the entity.
     */
    public function delete()
    {
        static::getManager()->delete($this);
    }

    /**
     * Gets model data as array.
     *
     * @param  array $data
     * @param  array $ignore
     * @return array
     */
    public function toArray(array $data = [], array $ignore = [])
    {
        $metadata = static::getMetadata();
        $mappings = $metadata->getRelationMappings();

        foreach (static::getProperties($this) as $name => $value) {

            if (isset($data[$name]) || isset($mappings[$name])) {
                continue;
            }

            switch ($metadata->getField($name, 'type')) {
                case 'json_array':
                    $value = $value ?: new \stdClass();
                    break;
                case 'datetime':
                    $value = $value ? $value->format(\DateTime::ATOM) : null;
                    break;
            }

            $data[$name] = $value;
        }

        return array_diff_key($data, array_flip($ignore));
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
