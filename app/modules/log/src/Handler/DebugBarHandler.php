<?php

namespace Pagekit\Log\Handler;

use DebugBar\DataCollector\DataCollectorInterface;
use Monolog\Handler\AbstractHandler;

class DebugBarHandler extends AbstractHandler implements DataCollectorInterface
{
    /**
     * @var array
     */
    protected $records = [];

    /**
     * {@inheritdoc}
     */
    public function handle(array $record)
    {
        if ($record['level'] < $this->level) {
            return false;
        }

        $keys = [
            'message',
            'level',
            'level_name',
            'channel'
        ];

        $this->records[] = array_intersect_key($record, array_flip($keys));

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function collect()
    {
        return ['records' => $this->records];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'log';
    }
}
