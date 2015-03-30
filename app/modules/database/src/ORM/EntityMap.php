<?php

namespace Pagekit\Database\ORM;

use Pagekit\Database\Events;

class EntityMap
{
    /**
     * @var EntityManager
     */
    protected $manager;

    /**
     * @var array
     */
    protected $entities = [];

    /**
     * @var array
     */
    protected $identifiers = [];

    /**
     * Constructor.
     *
     * @param EntityManager $manager
     */
    public function __construct(EntityManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Gets the identifier of an entity.
     *
     * @param  object $entity
     * @return mixed
     */
    public function getIdentifier($entity)
    {
        return $this->identifiers[spl_object_hash($entity)];
    }

    /**
     * Checks whether an entity is managed.
     *
     * @param  object $entity
     * @return bool
     */
    public function has($entity)
    {
        $hash     = spl_object_hash($entity);
        $metadata = $this->manager->getMetadata($entity);

        if (!isset($this->identifiers[$hash]) or '' === $this->identifiers[$hash]) {
            return false;
        }

        return isset($this->entities[$metadata->getClass()][$this->identifiers[$hash]]);
    }

    /**
     * Gets an entity.
     *
     * @param  int|string $id
     * @param  string     $class
     * @return mixed|false
     */
    public function get($id, $class)
    {
        if (isset($this->entities[$class][$id])) {
            return $this->entities[$class][$id];
        }

        return false;
    }

    /**
     * Registers an entity as managed.
     *
     * @param  object     $entity
     * @param  int|string $id
     * @throws \InvalidArgumentException
     * @return bool
     */
    public function add($entity, $id)
    {
        $metadata = $this->manager->getMetadata($entity);
        $class    = $metadata->getClass();

        if ($id === '') {
            throw new \InvalidArgumentException(sprintf('The entity of class %s is missing an identifier.', $class));
        }

        if (isset($this->entities[$class][$id])) {
            return false;
        }

        $this->identifiers[spl_object_hash($entity)] = $id;
        $this->entities[$class][$id] = $entity;

        return true;
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
        $id    = $data[$metadata->getIdentifier()];
        $class = $metadata->getClass();

        if (isset($this->entities[$class][$id])) {

            $entity = $this->entities[$class][$id];

        } else {

            $entity = $metadata->newInstance();
            $metadata->setValues($entity, $data, true, true);

            $this->identifiers[spl_object_hash($entity)] = $id;
            $this->entities[$class][$id] = $entity;
        }

        $this->manager->dispatchEvent(Events::postLoad, $entity, $metadata);

        return $entity;
    }

    /**
     * Removes an entity.
     *
     * @param  object $entity
     * @throws \InvalidArgumentException
     * @return bool
     */
    public function remove($entity)
    {
        $metadata = $this->manager->getMetadata($entity);
        $class    = $metadata->getClass();
        $id       = $this->getIdentifier($entity);

        if ($id === '') {
            throw new \InvalidArgumentException(sprintf('The entity of class %s is missing an identifier.', $class));
        }

        unset($this->identifiers[spl_object_hash($entity)]);

        if (isset($this->entities[$class][$id])) {
            unset($this->entities[$class][$id]);

            return true;
        }

        return false;
    }
}
