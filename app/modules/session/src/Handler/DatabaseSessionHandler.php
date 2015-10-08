<?php

namespace Pagekit\Session\Handler;

use Doctrine\DBAL\ConnectionException;
use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Pagekit\Database\Connection;

class DatabaseSessionHandler implements \SessionHandlerInterface
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
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function destroy($id)
    {
        try {
            $this->connection->delete($this->table, ['id' => $id]);
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
        try {
            $this->connection->executeQuery("DELETE FROM {$this->table} WHERE time < :time", ['time' => date('Y-m-d H:i:s', time() - $lifetime)]);
        } catch (\PDOException $e) {
            throw new \RuntimeException(sprintf('PDOException was thrown when trying to manipulate session data: %s', $e->getMessage()), 0, $e);
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function read($id)
    {
        try {

            $data = $this->connection->executeQuery("SELECT data FROM {$this->table} WHERE id = :id", ['id' => $id])->fetchAll(\PDO::FETCH_NUM);

            if ($data) {
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

            $params = ['id' => $id, 'data' => base64_encode($data), 'time' => date('Y-m-d H:i:s')];

            if (null !== $sql = $this->getMergeSql()) {
                $this->connection->executeQuery($sql, $params);
                return true;
            }

            $this->connection->beginTransaction();

            try {

                $this->connection->delete($this->table, ['id' => $id]);
                $this->connection->insert($this->table, $params);
                $this->connection->commit();

            } catch (ConnectionException $e) {
                $this->connection->rollback();

                throw $e;
            }

        } catch (\PDOException $e) {
            throw new \RuntimeException(sprintf('PDOException was thrown when trying to write the session data: %s', $e->getMessage()), 0, $e);
        }

        return true;
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
            return "INSERT INTO {$this->table} (id, data, time) VALUES (:id, :data, :time) "
            . "ON DUPLICATE KEY UPDATE data = VALUES(data), time = CASE WHEN time = :time THEN (VALUES(time) + INTERVAL 1 SECOND) ELSE VALUES(time) END";
        } elseif ($platform instanceof SqlitePlatform) {
            return  "INSERT OR REPLACE INTO {$this->table} (id, data, time) VALUES (:id, :data, :time)";
        }
    }
}
