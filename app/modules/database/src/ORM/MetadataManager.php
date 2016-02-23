<?php

namespace Pagekit\Database\ORM;

use Doctrine\Common\Cache\Cache;
use Pagekit\Database\Connection;
use Pagekit\Database\ORM\Loader\LoaderInterface;
use Pagekit\Event\EventDispatcherInterface;

class MetadataManager
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var EventDispatcherInterface
     */
    protected $events;

    /**
     * @var LoaderInterface
     */
    protected $loader;

    /**
     * @var Cache
     */
    protected $cache;

    /**
     * @var Metadata[]
     */
    protected $metadata = [];

    /**
     * The cache prefix
     *
     * @var string $prefix
     */
    protected $prefix = 'Metadata.';

    /**
     * Constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection, EventDispatcherInterface $events)
    {
        $this->connection = $connection;
        $this->events     = $events;
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
     * Sets the loader object used by the factory to create Metadata objects.
     *
     * @param LoaderInterface $loader
     */
    public function setLoader(LoaderInterface $loader)
    {
        $this->loader = $loader;
    }

    /**
     * Gets the cache used for caching Metadata objects.
     *
     * @return Cache
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * Sets the cache used for caching Metadata objects.
     *
     * @param Cache $cache
     */
    public function setCache(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Checks if the metadata for a class is already loaded.
     *
     * @param  string $class
     * @return bool
     */
    public function has($class)
    {
        return isset($this->metadata[$class]);
    }

    /**
     * Gets the metadata for the given class.
     *
     * @param  object|string $class
     * @return Metadata
     */
    public function get($class)
    {
        $class = new \ReflectionClass($class);
        $name  = $class->getName();

        if (!isset($this->metadata[$name])) {

            if ($this->cache) {

                $hash = filemtime($class->getFileName());
                foreach ($class->getTraits() as $trait) {
                    $hash += filemtime($trait->getFileName());
                }

                $current = $class;
                while ($parent = $current->getParentClass()) {
                    $hash += filemtime($parent->getFileName());
                    $current = $parent;
                }

                $id = sprintf('%s%s.%s', $this->prefix, $hash, $name);

                if ($config = $this->cache->fetch($id)) {
                    $this->metadata[$name] = new Metadata($this, $name, $config);
                } else {
                    $this->cache->save($id, $this->load($class)->getConfig());
                }

            } else {
                $this->load($class);
            }

            $this->subscribe($this->metadata[$name]);
        }

        return $this->metadata[$name];
    }

    /**
     * Loads the metadata of the given class.
     *
     * @param \ReflectionClass $class
     * @return Metadata
     */
    protected function load(\ReflectionClass $class)
    {
        $parent = null;

        foreach ($this->getParentClasses($class) as $class) {

            $name = $class->getName();

            if (isset($this->metadata[$name])) {
                $parent = $this->metadata[$name];
                continue;
            }

            $config = [];

            if ($parent) {

                foreach ($parent->getFields() as $field) {

                    if (!isset($field['inherited']) && !$parent->isMappedSuperclass()) {
                        $field['inherited'] = $parent->getClass();
                    }

                    $config['fields'][$field['name']] = $field;
                }

                foreach ($parent->getRelationMappings() as $relation) {

                    if (!isset($relation['inherited']) && !$parent->isMappedSuperclass()) {
                        $relation['inherited'] = $parent->getClass();
                    }

                    $config['relations'][$relation['name']] = $relation;
                }

                if ($identifier = $parent->getIdentifier()) {
                    $config['identifier'] = $identifier;
                }

                $config['events'] = $parent->getEvents();
            }

            $this->metadata[$name] = $parent = new Metadata($this, $name, $this->loader->load($class, $config));
        }

        return $parent;
    }

    /**
     * Get array of parent classes for the given class.
     *
     * @param  \ReflectionClass $class
     * @return array
     */
    protected function getParentClasses(\ReflectionClass $class)
    {
        $parents = [$class];

        while ($parent = $class->getParentClass()) {

            if (!$this->loader->isTransient($parent)) {
                array_unshift($parents, $parent);
            }

            $class = $parent;
        }

        return $parents;
    }

    /**
     * Subscribes model lifecycle callbacks.
     *
     * @param Metadata $metadata
     */
    protected function subscribe(Metadata $metadata)
    {
        foreach ($metadata->getEvents() as $event => $methods) {
            foreach ($methods as $method) {
                $this->events->on($metadata->getEventPrefix().'.'.$event, [$metadata->getClass(), $method]);
            }
        }
    }
}
