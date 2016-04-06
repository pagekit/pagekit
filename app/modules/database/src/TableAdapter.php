<?php

namespace Pagekit\Database;

class TableAdapter
{
    /**
     * @var Table
     */
    protected $table;

    /**
     * @var string
     */
    protected $name;

    /**
     * Constructor.
     *
     * @param Table $table
     * @param string $name
     */
    public function __construct($table, $name)
    {
        $this->table = $table;
        $this->name = $name;
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
        if ($this->name === 'sqlite' && $typeName === 'string' || $typeName === 'text') {
            if (!isset($options['customSchemaOptions'])) {
                $options['customSchemaOptions'] = [];
            }
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