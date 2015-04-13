<?php

namespace Pagekit\Config;

use Doctrine\Common\Cache\Cache;
use Doctrine\DBAL\Platforms\MySqlPlatform;
use Pagekit\Database\Connection;

class ConfigManager implements \IteratorAggregate
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var Cache
     */
    protected $cache;

    /**
     * @var string
     */
    protected $prefix = 'Options:';

    /**
     * @var array
     */
    protected $ignore = [];

    /**
     * @var array
     */
    protected $autoload = [];

    /**
     * @var array
     */
    protected $configs = [];

    /**
     * @var array
     */
    protected $protected = ['Ignore', 'Autoload'];

	/**
	 * @var string
	 */
	protected $table;

    /**
     * @var bool $initialized
     */
    protected $initialized = false;

    /**
     * Constructor.
     *
     * @param Connection $connection
     * @param Cache      $cache
     * @param string     $table
     */
    public function __construct(Connection $connection, Cache $cache, $table)
    {
        $this->connection = $connection;
        $this->cache      = $cache;
        $this->table      = $table;
    }

    /**
     * Get shortcut.
     *
     * @see get()
     */
    public function __invoke($name, $default = null)
    {
        return $this->get($name, $default);
    }

    /**
     * Gets all configs.
     *
     * @return array
     */
    public function all()
    {
        $this->initialize();

        return $this->configs;
    }

    /**
     * Gets all config names.
     *
     * @return array
     */
    public function keys()
    {
        return array_keys($this->all());
    }

    /**
     * Gets a config.
     *
     * @param  string $name
     * @throws \InvalidArgumentException
     * @return mixed
     */
    public function get($name)
    {
        $name = trim($name);

        if (empty($this->ignore) && $ignore = $this->cache->fetch($this->prefix.'Ignore')) {
            $this->ignore = $ignore ?: [];
        }

        if (empty($name) || isset($this->ignore[$name])) {
            return null;
        }

        $this->initialize(true);

        if (isset($this->configs[$name])) {
            return $this->configs[$name];
        }

        if ($config = $this->cache->fetch($this->prefix.$name)) {
            return $this->configs[$name] = new Config(json_decode($config, true));
        }

        if ($config = $this->connection->fetchAssoc("SELECT value FROM {$this->table} WHERE name = ?", [$name])) {
            $this->cache->save($this->prefix.$name, $config['value']);
            return $this->configs[$name] = new Config(json_decode($config['value'], true));
        }

        $this->ignore[$name] = true;
        $this->cache->save($this->prefix.'Ignore', $this->ignore);

        return null;
    }

    /**
     * Sets a config.
     *
     * @param  string  $name
     * @param  mixed   $config
     * @param  boolean $autoload
     * @throws \InvalidArgumentException
     */
    public function set($name, $config, $autoload = null)
    {
        $name = trim($name);

        if (empty($name)) {
            throw new \InvalidArgumentException('Empty option name given.');
        }

        if (in_array($name, $this->protected)) {
            throw new \InvalidArgumentException(sprintf('"%s" is a protected option and may not be modified.', $name));
        }

        if (is_array($config)) {
            $config = new Config($config);
        }

        $this->configs[$name] = $config;

        if ($config->dirty()) {

            $data = ['name' => $name, 'value' => json_encode($config)];

            if ($autoload !== null) {
                $data['autoload'] = $autoload ? '1' : '0';
            }

            if ($this->connection->getDatabasePlatform() instanceof MySqlPlatform) {

                if ($autoload === null) {
                    $query = "INSERT INTO {$this->table} (name, value) VALUES (:name, :value) ON DUPLICATE KEY UPDATE value = :value";
                } else {
                    $query = "INSERT INTO {$this->table} (name, value, autoload) VALUES (:name, :value, :autoload) ON DUPLICATE KEY UPDATE value = :value, autoload = :autoload";
                }

                $this->connection->executeQuery($query, $data);

            } elseif (!$this->connection->update($this->table, $data, compact('name'))) {

                $this->connection->insert($this->table, $data);

            }

            $this->cache->delete($this->prefix.(isset($this->autoload[$name]) ? 'Autoload' : $name));
        }

        if (isset($this->ignore[$name])) {
            unset($this->ignore[$name]);
            $this->cache->save($this->prefix.'Ignore', $this->ignore);
        }
    }

    /**
     * Removes a config.
     *
     * @param  string $name
     * @throws \InvalidArgumentException
     */
    public function remove($name)
    {
        $name = trim($name);

        if (empty($name)) {
            throw new \InvalidArgumentException('Empty name given.');
        }

        if (in_array($name, $this->protected)) {
            throw new \InvalidArgumentException(sprintf('"%s" is a protected and may not be modified.', $name));
        }

        $this->initialize(true);

        if ($this->connection->delete($this->table, ['name' => $name])) {
            unset($this->configs[$name]);
            $this->cache->delete($this->prefix.(isset($this->autoload[$name]) ? 'Autoload' : $name));
        }
    }

    /**
     * Returns an iterator.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->all());
    }

    /**
     * Initialize the all or only autoloads.
     *
     * @param bool $autoload
     */
    protected function initialize($autoload = false)
    {
        // TODO fix autoload
        return;

        if ($this->initialized) {
            return;
        }

        if ($autoload) {

            if (!$this->autoload and $configs = $this->cache->fetch($this->prefix.'Autoload')) {
                $this->configs = $this->autoload = $configs;
            }

            if ($this->autoload) {
                return;
            }

            $query = "SELECT name, value, autoload FROM {$this->table} WHERE autoload = 1";

        } else {

            $query = "SELECT name, value, autoload FROM {$this->table}";
        }

        if ($configs = $this->connection->fetchAll($query)) {

            foreach ($configs as $config) {

                $this->configs[$config['name']] = json_decode($config['value'], true);

                if ($config['autoload']) {
                    $this->autoload[$config['name']] = $this->configs[$config['name']];
                }
            }

            $this->cache->save($this->prefix.'Autoload', $this->autoload);

            if (!$autoload) {
                $this->initialized = true;
            }
        }
    }
}
