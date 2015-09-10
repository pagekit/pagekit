<?php

namespace Pagekit\Database\ORM;

use Pagekit\Database\Connection;
use Pagekit\Database\Events;
use Pagekit\Event\EventDispatcherInterface;
use Pagekit\Event\PrefixEventDispatcher;

class EntityManager
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var MetadataManager
     */
    protected $metadata;

    /**
     * @var EventDispatcherInterface
     */
    protected $events;

    /**
     * @var EntityManager
     */
    protected static $instance;

    /**
     * Creates a new Manager instance
     *
     * @param  Connection               $connection
     * @param  MetadataManager          $metadata
     * @param  EventDispatcherInterface $events
     */
    public function __construct(Connection $connection, MetadataManager $metadata, EventDispatcherInterface $events = null)
    {
        $this->connection = $connection;
        $this->metadata   = $metadata;
        $this->events     = $events ?: new PrefixEventDispatcher('model.');

        static::$instance = $this;
    }

    /**
     * Gets the database connection.
     *
     * @return Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Gets the metadata object of an entity class.
     *
     * @param  mixed $class
     * @return Metadata
     */
    public function getMetadata($class)
    {
        return $this->metadata->get($class);
    }

    /**
     * Gets the metadata manager.
     *
     * @return MetadataManager
     */
    public function getMetadataManager()
    {
        return $this->metadata;
    }

    /**
     * Retrieve an entity by its identifier.
     *
     * @param  string $entity
     * @param  mixed  $identifier
     * @return mixed
     */
    public function find($entity, $identifier)
    {
        $callable = "{$entity}::find";
        if (is_callable($callable)) {
            return call_user_func($callable, $identifier);
        }
    }

    /**
     * Checks whether the given managed entity exists in the database.
     *
     * @param  object $entity
     * @return bool
     */
    public function exists($entity)
    {
        $metadata   = $this->getMetadata($entity);
        $identifier = $metadata->getIdentifier(true);

        if (empty($identifier)) {
            return false;
        }

        return (bool) $this->connection->fetchColumn('SELECT 1 FROM '.$metadata->getTable().' WHERE '.$identifier.'='.$this->connection->quote($metadata->getValue($entity, $identifier, true)));
    }

    /**
     * Relate target entities to the entity's relation.
     *
     * @param  array        $entities
     * @param  string       $name
     * @param  QueryBuilder $query
     * @throws \LogicException
     */
    public function related($entities, $name, QueryBuilder $query)
    {
        if (!is_array($entities)) {
            $entities = [$entities];
        }

        $metadata = $this->getMetadata(current($entities));
        $mapping  = $metadata->getRelationMapping($name);

        if (!class_exists($class = 'Pagekit\Database\ORM\\Relation\\'.$mapping['type'])) {
            throw new \LogicException(sprintf("Unable to find relation class '%s'", $class));
        }

        $relation = new $class($this, $metadata, $mapping);
        $relation->resolve($entities, $query);
    }

    /**
     * Saves an entity.
     *
     * @param object $entity
     * @param array  $data
     */
    public function save($entity, array $data = [])
    {
        $metadata   = $this->getMetadata($entity);
        $identifier = $metadata->getIdentifier(true);

        $metadata->setValues($entity, $data, false, true);

        $this->trigger(Events::SAVING, $metadata, [$entity, $data]);

        if (!$id = $metadata->getValue($entity, $identifier, true)) {

            $this->trigger(Events::CREATING, $metadata, [$entity, $data]);

            $this->connection->insert($metadata->getTable(), $metadata->getValues($entity, true, true));

            $metadata->setValue($entity, $identifier, $this->connection->lastInsertId(), true, true);

            $this->trigger(Events::CREATED, $metadata, [$entity, $data]);

        } else {

            $this->trigger(Events::UPDATING, $metadata, [$entity, $data]);

            $this->connection->update($metadata->getTable(), $metadata->getValues($entity, true, true), [$identifier => $id]);

            $this->trigger(Events::UPDATED, $metadata, [$entity, $data]);
        }

        $this->trigger(Events::SAVED, $metadata, [$entity, $data]);
    }

    /**
     * Deletes an entity.
     *
     * @param  object $entity
     * @throws \InvalidArgumentException
     */
    public function delete($entity)
    {
        $metadata   = $this->getMetadata($entity);
        $identifier = $metadata->getIdentifier(true);

        if ($value = $metadata->getValue($entity, $identifier, true)) {

            $this->trigger(Events::DELETING, $metadata, [$entity]);

            $this->connection->delete($metadata->getTable(), [$identifier => $value]);

            $this->trigger(Events::DELETED, $metadata, [$entity]);

            $metadata->setValue($entity, $identifier, null, true);

        } else {
            throw new \InvalidArgumentException("Can't remove entity with empty identifier value.");
        }
    }

    /**
     * Hydrates only one row of the passed statement.
     *
     * @param  object   $statement
     * @param  Metadata $metadata
     * @return mixed
     */
    public function hydrateOne($statement, Metadata $metadata)
    {
        if ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
            return $this->load($metadata, $row, true, true);
        }

        return false;
    }

    /**
     * Hydrates all rows returned by the passed statement instance at once.
     *
     * @param  object   $statement
     * @param  Metadata $metadata
     * @return mixed
     */
    public function hydrateAll($statement, Metadata $metadata)
    {
        $result     = [];
        $identifier = $metadata->getIdentifier();

        while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
            $entity = $this->load($metadata, $row, true, true);
            $result[$metadata->getValue($entity, $identifier)] = $entity;
        }

        return $result;
    }

    /**
     * Loads an entity or creates a new one if it does not already exist.
     *
     * @param  Metadata $metadata
     * @param  array    $data
     * @param  bool     $column
     * @param  bool     $convert
     * @return object
     */
    public function load(Metadata $metadata, array $data, $column = false, $convert = false)
    {
        $entity = $metadata->newInstance();
        $metadata->setValues($entity, $data, $column, $convert);

        $this->trigger(Events::INIT, $metadata, [$entity]);

        return $entity;
    }

    /**
     * Dispatches an event to all registered listeners.
     *
     * @param  string   $name
     * @param  Metadata $metadata
     * @param  array    $arguments
     * @return bool
     */
    public function trigger($name, Metadata $metadata, array $arguments)
    {
        $this->events->trigger("{$metadata->getEventPrefix()}.{$name}", $arguments);
    }

    /**
     * Gets the instance.
     *
     * @return EntityManager
     */
    public static function getInstance()
    {
        return static::$instance;
    }
}
