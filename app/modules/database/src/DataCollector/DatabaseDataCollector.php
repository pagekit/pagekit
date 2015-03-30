<?php

namespace Pagekit\Database\DataCollector;

use Doctrine\DBAL\Logging\DebugStack;
use Doctrine\DBAL\Types\Type;
use Pagekit\Database\Connection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

class DatabaseDataCollector extends DataCollector
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var DebugStack
     */
    protected $logger;

    /**
     * Constructor.
     *
     * @param Connection $connection
     * @param DebugStack $logger
     */
    public function __construct(Connection $connection, DebugStack $logger = null)
    {
        $this->connection = $connection;
        $this->logger     = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data = [
            'queries' => null !== $this->logger ? $this->sanitizeQueries($this->logger->queries) : [],
            'driver'  => $this->connection->getDriver()->getName()
        ];
    }

    /**
     * Gets the logged query count
     *
     * @return int
     */
    public function getQueryCount()
    {
        return count($this->data['queries']);
    }

    /**
     * Gets the logged queries
     *
     * @return array
     */
    public function getQueries()
    {
        return $this->data['queries'];
    }

    /**
     * Get connection driver
     *
     * @return string
     */
    public function getDriver()
    {
        return $this->data['driver'];
    }


    /**
     * Gets the total execution time of logged queries
     *
     * @return int
     */
    public function getTime()
    {
        $time = 0;

        foreach ($this->data['queries'] as $query) {
            $time += $query['executionMS'];
        }

        return $time;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'db';
    }

    public function explain($query)
    {
        if (!$query['explainable']) {
            return false;
        }

        try {

            return $this->connection->executeQuery('EXPLAIN '.$query['sql'], $query['params'], isset($query['types']) ? $query['types'] : [])->fetchAll(\PDO::FETCH_ASSOC);

        } catch (\Exception $e) {
            return false;
        }
    }

    private function sanitizeQueries($queries)
    {
        foreach ($queries as $i => $query) {
            $queries[$i] = $this->sanitizeQuery($query);
        }

        return $queries;
    }

    private function sanitizeQuery($query)
    {
        $query['explainable'] = true;
        $query['params'] = (array) $query['params'];
        foreach ($query['params'] as $j => &$param) {
            $key = is_int($j) ? $j + 1 : $j;
            if (isset($query['types'][$key])) {
                // Transform the param according to the type
                $type = $query['types'][$key];
                if (is_string($type)) {
                    $type = Type::getType($type);
                }
                if ($type instanceof Type) {
                    $query['types'][$key] = $type->getBindingType();
                    $param = $type->convertToDatabaseValue($param, $this->connection->getDatabasePlatform());
                }
            }

            list($param, $explainable) = $this->sanitizeParam($param);
            if (!$explainable) {
                $query['explainable'] = false;
            }
        }

        if ($query['explainable'] && $explanation = $this->explain($query)) {
            $query['explain'] = $explanation;
        }

        return $query;
    }

    /**
     * Sanitizes a param.
     *
     * The return value is an array with the sanitized value and a boolean
     * indicating if the original value was kept (allowing to use the sanitized
     * value to explain the query).
     *
     * @param mixed $var
     *
     * @return array
     */
    private function sanitizeParam($var)
    {
        if (is_object($var)) {
            return [sprintf('Object(%s)', get_class($var)), false];
        }

        if (is_array($var)) {
            $a = [];
            $original = true;
            foreach ($var as $k => $v) {
                list($value, $orig) = $this->sanitizeParam($v);
                $original = $original && $orig;
                $a[$k] = $value;
            }

            return [$a, $original];
        }

        if (is_resource($var)) {
            return [sprintf('Resource(%s)', get_resource_type($var)), false];
        }

        return [$var, true];
    }
}
