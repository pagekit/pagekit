<?php

namespace Pagekit\Database\ORM;

use Pagekit\Database\Connection;
use Pagekit\Database\Event\EntityEvent;
use Pagekit\Database\Events;

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
     * @var string
     */
    protected $eventClass;

    /**
     * @var EntityManager
     */
    protected static $instance;

    /**
     * Creates a new Manager instance
     *
     * @param  Connection      $connection
     * @param  MetadataManager $metadata
     * @param  string          $eventClass
     * @throws \RuntimeException
     */
    public function __construct(Connection $connection, MetadataManager $metadata, $eventClass = 'Pagekit\Database\Event\EntityEvent')
    {
        if (!is_a($eventClass, 'Pagekit\Database\Event\EntityEvent', true)) {
            throw new \RuntimeException(sprintf('The Event Class %s is not a subclass of "Pagekit\Database\Event\EntityEvent"', $eventClass));
        }

        $this->connection = $connection;
        $this->metadata   = $metadata;
        $this->eventClass = $eventClass;

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
            return call_user_func("{$entity}::find", $identifier);
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

        $metadata->setValues($entity, $data);

        $this->dispatchEvent(Events::preSave, $entity, $metadata);

        if (!$id = $metadata->getValue($entity, $identifier, true)) {


            $this->dispatchEvent(Events::preCreate, $entity, $metadata);

            $this->connection->insert($metadata->getTable(), $metadata->getValues($entity, true, true));

            $metadata->setValue($entity, $identifier, $this->connection->lastInsertId(), true);

            $this->dispatchEvent(Events::postCreate, $entity, $metadata);

        } else {

            $this->dispatchEvent(Events::preUpdate, $entity, $metadata);

            $this->connection->update($metadata->getTable(), $metadata->getValues($entity, true, true), [$identifier => $id]);

            $this->dispatchEvent(Events::postUpdate, $entity, $metadata);
        }

        $this->dispatchEvent(Events::postSave, $entity, $metadata);
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

            $this->dispatchEvent(Events::preDelete, $entity, $metadata);

            $this->connection->delete($metadata->getTable(), [$identifier => $value]);

            $this->dispatchEvent(Events::postDelete, $entity, $metadata);

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
            return $this->load($metadata, $row);
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
            $entity = $this->load($metadata, $row);
            $result[$metadata->getValue($entity, $identifier)] = $entity;
        }

        return $result;
    }

    /**
     * Loads an entity or creates a new one if it does not already exist.
     *
     * @param  Metadata $metadata
     * @param  array    $data
     * @return object
     */
    public function load(Metadata $metadata, array $data)
    {
        $entity = $metadata->newInstance();
        $metadata->setValues($entity, $data, true, true);

        $this->dispatchEvent(Events::postLoad, $entity, $metadata);

        return $entity;
    }

    /**
     * Dispatches an event to all registered listeners.
     *
     * @param  string   $name
     * @param  mixed    $entity
     * @param  Metadata $metadata
     * @return bool
     */
    public function dispatchEvent($name, $entity, Metadata $metadata)
    {
        $prefix = $metadata->getEventPrefix();
        $event  = new $this->eventClass(($prefix ? $prefix.'.' : '').$name, $entity, $metadata, $this);

        if ($events = $metadata->getEvents() and isset($events[$name])) {
            foreach ($events[$name] as $callback) {
                call_user_func_array([$entity, $callback], [$event]);
            }
        }

        $this->connection->getEventDispatcher()->trigger($event, [$entity]);
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
