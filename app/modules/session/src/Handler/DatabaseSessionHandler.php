<?php

namespace Pagekit\Session\Handler;

use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Pagekit\Database\Connection;
use Symfony\Component\HttpFoundation\Session\Storage\Proxy\SessionHandlerProxy;

class DatabaseSessionHandler extends SessionHandlerProxy
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
     * @var bool Whether gc() has been called
     */
    protected $gcCalled = false;

    /**
     * Constructor.
     *
     * @param Connection $connection
     * @param string $table
     */
    public function __construct(Connection $connection, $table = 'sessions')
    {
        $this->connection = $connection;
        $this->table = $table;
    }

    /**
     * {@inheritdoc}
     */
    public function open($path = null, $name = null)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        if ($this->gcCalled) {
            try {

                $platform = $this->connection->getDatabasePlatform();

                if ($platform instanceof MySqlPlatform) {
                    $this->connection->executeQuery("DELETE FROM {$this->table} WHERE DATE_ADD(time, INTERVAL lifetime SECOND) < :time", ['time' => date('Y-m-d H:i:s')]);
                } elseif ($platform instanceof SqlitePlatform) {
                    $this->connection->executeQuery("DELETE FROM {$this->table} WHERE (CAST(strftime('%s', time) AS integer) + lifetime) < :time", ['time' => time()], ['time' => \PDO::PARAM_INT]);
                }

            } catch (\PDOException $e) {
            }
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function destroy($id)
    {
        try {
            $this->connection->delete($this->table, ['id' => sha1($id)]);
        } catch (\PDOException $e) {
            throw new \RuntimeException(sprintf('PDOException was thrown when trying to manipulate session data: %s', $e->getMessage()), 0, $e);
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function gc($lifetime)
    {
        return $this->gcCalled = true;
    }

    /**
     * {@inheritdoc}
     */
    public function read($id)
    {
        try {

            $data = $this->connection->executeQuery("SELECT data, lifetime FROM {$this->table} WHERE id = :id", ['id' => sha1($id)])->fetchAll(\PDO::FETCH_NUM);

            if ($data) {
                $this->setLifetime($data[0][1]);

                return base64_decode($data[0][0]);
            }

            return '';

        } catch (\PDOException $e) {
            throw new \RuntimeException(sprintf('PDOException was thrown when trying to read the session data: %s', $e->getMessage()), 0, $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function write($id, $data)
    {
        try {

            $params = ['id' => sha1($id), 'data' => base64_encode($data), 'time' => date('Y-m-d H:i:s'), 'lifetime' => $this->getLifetime()];
            $sql = $this->getMergeSql();

            $this->connection->executeQuery($sql, $params);

        } catch (\PDOException $e) {
            throw new \RuntimeException(sprintf('PDOException was thrown when trying to write the session data: %s', $e->getMessage()), 0, $e);
        }

        return true;
    }

    /**
     * @return int
     */
    protected function getLifetime()
    {
        return (int)ini_get('session.gc_maxlifetime');
    }

    /**
     * @param int $lifetime
     */
    protected function setLifetime($lifetime)
    {
        ini_set('session.gc_maxlifetime', $lifetime);
    }

    /**
     * Returns a merge/upsert (i.e. insert or update) SQL query when supported by the database.
     *
     * @return string|null The SQL string or null when not supported
     */
    protected function getMergeSql()
    {
        $platform = $this->connection->getDatabasePlatform();

        if ($platform instanceof MySqlPlatform) {
            return "INSERT INTO {$this->table} (id, data, time, lifetime) VALUES (:id, :data, :time , :lifetime) "
            . "ON DUPLICATE KEY UPDATE data = VALUES(data), time = CASE WHEN time = :time THEN (VALUES(time) + INTERVAL 1 SECOND) ELSE VALUES(time) END";
        } elseif ($platform instanceof SqlitePlatform) {
            return "INSERT OR REPLACE INTO {$this->table} (id, data, time, lifetime) VALUES (:id, :data, :time, :lifetime)";
        }

        throw new \RuntimeException('Not supported database.');
    }
}
