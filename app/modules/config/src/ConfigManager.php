<?php

namespace Pagekit\Config;

use Doctrine\DBAL\Platforms\MySqlPlatform;
use Pagekit\Database\Connection;

class ConfigManager implements \IteratorAggregate
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var string
     */
    protected $table;

    /**
     * @var string
     */
    protected $cache;

    /**
     * @var string
     */
    protected $prefix;

    /**
     * @var Config
     */
    protected $ignore;

    /**
     * @var array
     */
    protected $configs = [];

    /**
     * Constructor.
     *
     * @param Connection $connection
     * @param array      $config
     */
    public function __construct(Connection $connection, array $config)
    {
        $this->connection = $connection;
        $this->table      = $config['table'];
        $this->cache      = $config['cache'];
        $this->prefix     = $config['prefix'];
        $this->ignore     = new Config($this->getCache('_ignore') ?: []);
    }

    /**
     * Get shortcut.
     *
     * @see get()
     */
    public function __invoke($name)
    {
        return $this->get($name);
    }

    /**
     * Gets a config.
     *
     * @param  string $name
     * @return Config
     */
    public function get($name)
    {
        if (empty($name) || isset($this->ignore[$name])) {
            return null;
        }

        if (isset($this->configs[$name])) {
            return $this->configs[$name];
        }

        if ($values = $this->getCache($name)) {
            return $this->configs[$name] = new Config($values);
        }

        if ($data = $this->connection->fetchAssoc("SELECT value FROM {$this->table} WHERE name = ?", [$name])) {
            $this->writeCache($name, $config = new Config(json_decode($data['value'], true)));
            return $this->configs[$name] = $config;
        }

        $this->ignore[$name] = true;
        $this->writeCache('_ignore', $this->ignore);

        return null;
    }

    /**
     * Sets a config.
     *
     * @param string $name
     * @param mixed  $config
     */
    public function set($name, $config)
    {
        if (is_array($config)) {
            $config = (new Config())->merge($config);
        }

        $this->configs[$name] = $config;

        if ($config->dirty()) {

            $data = ['name' => $name, 'value' => json_encode($config)];

            if ($this->connection->getDatabasePlatform() instanceof MySqlPlatform) {
                $this->connection->executeQuery("INSERT INTO {$this->table} (name, value) VALUES (:name, :value) ON DUPLICATE KEY UPDATE value = :value", $data);
            } elseif (!$this->connection->update($this->table, $data, compact('name'))) {
                $this->connection->insert($this->table, $data);
            }

            $this->removeCache($name);
        }

        if (isset($this->ignore[$name])) {
            unset($this->ignore[$name]);
            $this->writeCache('_ignore', $this->ignore);
        }
    }

    /**
     * Removes a config.
     *
     * @param string $name
     */
    public function remove($name)
    {
        if ($this->connection->delete($this->table, ['name' => $name])) {
            $this->removeCache($name);
        }
    }

    /**
     * Returns an iterator.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->configs);
    }

    /**
     * Gets cache data.
     *
     * @param  string $name
     * @return array|null
     */
    protected function getCache($name)
    {
        $file = sprintf('%s/%s.cache', $this->cache, sha1($this->prefix.$name));

        if ($this->cache && file_exists($file)) {
            return require $file;
        }
    }

    /**
     * Writes cache file.
     *
     * @param  string $name
     * @param  Config $config
     * @throws \RuntimeException
     */
    protected function writeCache($name, $config)
    {
        $file = sprintf('%s/%s.cache', $this->cache, sha1($this->prefix.$name));

        if (count($config) && !file_put_contents($file, $config->dump())) {
            throw new \RuntimeException("Failed to write cache file ($file).");
        }

        if (function_exists('opcache_invalidate')) {
            opcache_invalidate($file);
        }
    }

    /**
     * Removes cache file.
     *
     * @param string $name
     */
    protected function removeCache($name)
    {
        $file = sprintf('%s/%s.cache', $this->cache, sha1($this->prefix.$name));

        if ($this->cache && file_exists($file)) {
            unlink($file);
        }

        if (function_exists('opcache_invalidate')) {
            opcache_invalidate($file);
        }
    }
}
