<?php

namespace Pagekit\Debug\DataCollector;

use DebugBar\Bridge\DoctrineCollector;
use Doctrine\DBAL\Logging\DebugStack;
use Pagekit\Database\Connection;

class DatabaseDataCollector extends DoctrineCollector
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * Constructor.
     *
     * @param Connection $connection
     * @param DebugStack $debugStack
     */
    public function __construct(Connection $connection, DebugStack $debugStack = null)
    {
        $this->connection = $connection;
        $this->debugStack = $debugStack;
    }

    /**
     * {@inheritdoc}
     */
    public function collect()
    {
        $driver = $this->connection->getDriver()->getName();

        return array_replace(compact('driver'), parent::collect());
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'database';
    }
}
