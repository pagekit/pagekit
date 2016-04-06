<?php

namespace Pagekit\Database;

class Table
{
    /**
     * @var Table
     */
    protected $table;

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * Constructor.
     *
     * @param Table $table
     * @param Connection $connection
     */
    public function __construct($table, $connection)
    {
        $this->table = $table;
        $this->connection = $connection;
    }

    /**
     * @param array $columnNames
     * @param string|null $indexName
     * @param array $flags
     * @param array $options
     *
     * @return self
     */
    public function addIndex(array $columnNames, $indexName = null, array $flags = array(), array $options = array())
    {
        if ($indexName) {
            $indexName = $this->connection->replacePrefix($indexName);
        }

        $this->table->addIndex($columnNames, $indexName, $flags, $options);

        return $this;
    }

    /**
     * @param array $columnNames
     * @param string|null $indexName
     * @param array $options
     *
     * @return self
     */
    public function addUniqueIndex(array $columnNames, $indexName = null, array $options = array())
    {
        if ($indexName) {
            $indexName = $this->connection->replacePrefix($indexName);
        }

        $this->table->addUniqueIndex($columnNames, $indexName, $options);

        return $this;
    }

    /**
     * @param string $columnName
     * @param string $typeName
     * @param array $options
     *
     * @return Column
     */
    public function addColumn($columnName, $typeName, array $options = array())
    {
        if ($this->connection->getDatabasePlatform()->getName() === 'sqlite' && in_array($typeName, ['string', 'text'])) {
            $options['customSchemaOptions']['collation'] = 'NOCASE';
        }

        return $this->table->addColumn($columnName, $typeName, $options);
    }

    /**
     * Proxy method call to table.
     *
     * @param  string $method
     * @param  array $args
     * @throws \BadMethodCallException
     * @return mixed
     */
    public function __call($method, $args)
    {
        if (!method_exists($this->table, $method)) {
            throw new \BadMethodCallException(sprintf('Undefined method call "%s::%s"', get_class($this->table), $method));
        }

        return call_user_func_array([$this->table, $method], $args);
    }
}