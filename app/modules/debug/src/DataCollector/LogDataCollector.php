<?php

namespace Pagekit\Debug\DataCollector;

use DebugBar\DataCollector\DataCollectorInterface;
use Monolog\Handler\AbstractHandler;

class LogDataCollector extends AbstractHandler implements DataCollectorInterface
{
    /**
     * @var array
     */
    protected $messages = [];

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

        $this->messages[] = array_intersect_key($record, array_flip($keys));

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function collect()
    {
        return ['messages' => $this->messages];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'log';
    }
}
