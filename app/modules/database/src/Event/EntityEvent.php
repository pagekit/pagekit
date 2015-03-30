<?php

namespace Pagekit\Database\Event;

use Pagekit\Database\Connection;
use Pagekit\Database\ORM\EntityManager;
use Pagekit\Database\ORM\Metadata;
use Symfony\Component\EventDispatcher\Event;

class EntityEvent extends Event
{
    /**
     * @var object
     */
    protected $entity;

    /**
     * @var Metadata
     */
    protected $metadata;

    /**
     * @var EntityManager
     */
    protected $manager;

    /**
     * Constructor.
     *
     * @param object        $entity
     * @param Metadata      $metadata
     * @param EntityManager $manager
     */
    public function __construct($entity, Metadata $metadata, EntityManager $manager)
    {
        $this->entity   = $entity;
        $this->metadata = $metadata;
        $this->manager  = $manager;
    }

    /**
     * Returns the entity object for this event.
     *
     * @return object
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Returns the metadata object for the entity.
     *
     * @return Metadata
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * Returns the entity manager for this event.
     *
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->manager;
    }

    /**
     * Returns the database connection for this event.
     *
     * @return Connection
     */
    public function getConnection()
    {
        return $this->manager->getConnection();
    }
}
