<?php

namespace Pagekit\Database\Logging;

use Doctrine\DBAL\Logging\DebugStack as BaseDebugStack;
use Symfony\Component\Stopwatch\Stopwatch;

class DebugStack extends BaseDebugStack
{
    /**
     * @var string
     */
    protected $callstack;

    /**
    * @var Stopwatch
    */
    protected $stopwatch;

    public function __construct(Stopwatch $stopwatch = null)
    {
        $this->stopwatch = $stopwatch;
    }

    /**
     * {@inheritdoc}
     */
    public function startQuery($sql, array $params = null, array $types = null)
    {
        if ($this->enabled) {
            $e = new \Exception;
            $this->callstack = $e->getTraceAsString();
        }

        if (null !== $this->stopwatch) {
            $this->stopwatch->start('doctrine');
        }

        parent::startQuery($sql, $params, $types);
    }

    /**
     * {@inheritdoc}
     */
    public function stopQuery()
    {
        parent::stopQuery();

        if (null !== $this->stopwatch) {
            $this->stopwatch->stop('doctrine');
        }
        
        if ($this->enabled) {
            $this->queries[$this->currentQuery]['callstack'] = $this->callstack;
        }
    }
}
