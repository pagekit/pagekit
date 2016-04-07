<?php

namespace Pagekit\Database;

use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Comparator;
use Doctrine\DBAL\Schema\Constraint;
use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use Doctrine\DBAL\Schema\Index;
use Doctrine\DBAL\Schema\Schema;

class Utility
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var AbstractSchemaManager
     */
    protected $manager;

    /**
     * @var Schema
     */
    protected $schema;

    /**
     * Constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->manager = $this->connection->getSchemaManager();
        $this->schema = $this->manager->createSchema();
    }

    /**
     * Return the DBAL schema manager.
     *
     * @return Doctrine\DBAL\Schema\AbstractSchemaManager
     */
    public function getSchemaManager()
    {
        return $this->manager;
    }

    /**
     * Returns true if the given table exists.
     *
     * @param  string $table
     * @return bool
     */
    public function tableExists($table)
    {
        return $this->tablesExist($table);
    }

    /**
     * Returns an existing database table.
     *
     * @param string $table
     *
     * @return Table
     *
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function getTable($table)
    {
        return new Table($this->schema->getTable($this->replacePrefix($table)), $this->connection);
    }

    /**
     * Returns true if all the given tables exist.
     *
     * @param  array $tables
     * @return bool
     */
    public function tablesExist($tables)
    {
        $tables = array_map([$this, 'replacePrefix'], (array) $tables);

        return $this->manager->tablesExist($tables);
    }

    /**
     * Creates a new database table.
     *
     * @param string $table
     * @param \Closure $callback
     */
    public function createTable($table, \Closure $callback)
    {
        $table = $this->schema->createTable($this->replacePrefix($table));

        $callback(new Table($table, $this->connection));

        $this->manager->createTable($table);
    }

    /**
     * {@see AbstractSchemaManager::createConstraint}
     */
    public function createConstraint(Constraint $constraint, $table)
    {
        $this->manager->createConstraint($constraint, $this->replacePrefix($table));
    }

    /**
     * {@see AbstractSchemaManager::createIndex}
     */
    public function createIndex(Index $index, $table)
    {
        $this->manager->createIndex($index, $this->replacePrefix($table));
    }

    /**
     * {@see AbstractSchemaManager::createForeignKey}
     */
    public function createForeignKey(ForeignKeyConstraint $foreignKey, $table)
    {
        $this->manager->createForeignKey($foreignKey, $this->replacePrefix($table));
    }

    /**
     * {@see AbstractSchemaManager::dropAndCreateConstraint}
     */
    public function dropAndCreateConstraint(Constraint $constraint, $table)
    {
        $this->manager->dropAndCreateConstraint($constraint, $this->replacePrefix($table));
    }

    /**
     * {@see AbstractSchemaManager::dropAndCreateIndex}
     */
    public function dropAndCreateIndex(Index $index, $table)
    {
        $this->manager->dropAndCreateIndex($index, $this->replacePrefix($table));
    }

    /**
     * {@see AbstractSchemaManager::dropAndCreateForeignKey}
     */
    public function dropAndCreateForeignKey(ForeignKeyConstraint $foreignKey, $table)
    {
        $this->manager->dropAndCreateForeignKey($foreignKey, $this->replacePrefix($table));
    }

    /**
     * {@see AbstractSchemaManager::dropAndCreateTable}
     */
    public function dropAndCreateTable($table)
    {
        $this->manager->dropAndCreateTable($table);
    }

    /**
     * {@see AbstractSchemaManager::renameTable}
     */
    public function renameTable($name, $newName)
    {
        $this->manager->renameTable($this->replacePrefix($name), $this->replacePrefix($newName));
    }

    /**
     * @see AbstractSchemaManager::dropTable
     */
    public function dropTable($table)
    {
        $this->manager->dropTable($this->replacePrefix($table));
    }

    /**
     * {@see AbstractSchemaManager::dropIndex}
     */
    public function dropIndex($index, $table)
    {
        $this->manager->dropIndex($index, $this->replacePrefix($table));
    }

    /**
     * {@see AbstractSchemaManager::dropConstraint}
     */
    public function dropConstraint(Constraint $constraint, $table)
    {
        $this->manager->dropConstraint($constraint, $this->replacePrefix($table));
    }

    /**
     * {@see AbstractSchemaManager::dropForeignKey}
     */
    public function dropForeignKey($foreignKey, $table)
    {
        $this->manager->dropForeignKey($foreignKey, $this->replacePrefix($table));
    }

    /**
     * {@see AbstractSchemaManager::listTableColumns}
     */
    public function listTableColumns($table, $database = null)
    {
        return $this->manager->listTableColumns($this->replacePrefix($table), $database);
    }

    /**
     * {@see AbstractSchemaManager::listTableIndexes}
     */
    public function listTableIndexes($table)
    {
        return $this->manager->listTableIndexes($this->replacePrefix($table));
    }

    /**
     * {@see AbstractSchemaManager::listTableDetails}
     */
    public function listTableDetails($tableName)
    {
        return $this->manager->listTableDetails($this->replacePrefix($tableName));
    }

    /**
     * {@see AbstractSchemaManager::listTableForeignKeys}
     */
    public function listTableForeignKeys($table, $database = null)
    {
        return $this->manager->listTableForeignKeys($this->replacePrefix($table), $database);
    }

    /**
     * Proxy method call to database schema manager.
     *
     * @param  string $method
     * @param  array $args
     * @throws \BadMethodCallException
     * @return mixed
     */
    public function __call($method, $args)
    {
        if (!method_exists($this->manager, $method)) {
            throw new \BadMethodCallException(sprintf('Undefined method call "%s::%s"', get_class($this->manager), $method));
        }

        return call_user_func_array([$this->manager, $method], $args);
    }

    /**
     * Migrates the database.
     *
     * @return Schema
     */
    public function migrate() {
        $diff = Comparator::compareSchemas($this->manager->createSchema(), $this->schema);

        foreach ($diff->toSaveSql($this->connection->getDatabasePlatform()) as $query) {
            $this->connection->executeQuery($query);
        }
    }

    /**
     * Replaces the table prefix placeholder with actual one.
     *
     * @param  string $query
     * @return string
     */
    protected function replacePrefix($query)
    {
        return $this->connection->replacePrefix($query);
    }
}
