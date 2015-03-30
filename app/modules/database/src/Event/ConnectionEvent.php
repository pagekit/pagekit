<?php

namespace Pagekit\Database\Event;

use Pagekit\Database\Connection;
use Symfony\Component\EventDispatcher\Event;

class ConnectionEvent extends Event
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @return \Doctrine\DBAL\Driver
     */
    public function getDriver()
    {
        return $this->connection->getDriver();
    }

    /**
     * @return \Doctrine\DBAL\Platforms\AbstractPlatform
     */
    public function getDatabasePlatform()
    {
        return $this->connection->getDatabasePlatform();
    }

    /**
     * @return \Doctrine\DBAL\Schema\AbstractSchemaManager
     */
    public function getSchemaManager()
    {
        return $this->connection->getSchemaManager();
    }
}
