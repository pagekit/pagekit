<?php

namespace Pagekit\Debug\DataCollector;

use DebugBar\DataCollector\DataCollectorInterface;
use Pagekit\Debug\Event\TraceableEventDispatcher;
use Pagekit\Event\EventDispatcherInterface;

class EventDataCollector implements DataCollectorInterface
{
    protected $data = [];
    protected $dispatcher;
    protected $base;

    public function __construct(EventDispatcherInterface $dispatcher, $base = '')
    {
        $this->dispatcher = $dispatcher;
        $this->base = $base;
    }

    /**
     * {@inheritdoc}
     */
    public function collect()
    {
        if ($this->dispatcher instanceof TraceableEventDispatcher) {
            $this->data['called'] = $this->attachLink($this->dispatcher->getCalledListeners());
            $this->data['notcalled'] = $this->attachLink($this->dispatcher->getNotCalledListeners());
        }

        return $this->data;
    }

    public function attachLink($listeners)
    {
        foreach ($listeners as &$listener) {
            if (isset($listener['file'], $listener['line'])) {
                $listener['relative'] = substr($listener['file'], strlen($this->base) + 1);
                $listener['link'] = $this->getFileLink($listener['file'], $listener['line']);
            }
        }

        return $listeners;
    }

    protected function getFileLink($file, $line)
    {
        if ($fileLinkFormat = ini_get('xdebug.file_link_format') and file_exists($file)) {
            return strtr($fileLinkFormat, array('%f' => $file, '%l' => $line));
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'events';
    }
}
