<?php

namespace Pagekit\Profiler;

use Symfony\Component\HttpKernel\DataCollector\DataCollectorInterface;
use Symfony\Component\HttpKernel\Profiler\Profiler as BaseProfiler;

class Profiler extends BaseProfiler
{
    /**
     * @var string[]
     */
    protected $views = [];

    /**
     * @var array
     */
    protected $order = [];

    /**
     * Adds a Collector.
     *
     * @param DataCollectorInterface $collector
     * @param string                 $toolbar
     * @param string                 $panel
     * @param int                    $priority
     */
    public function add(DataCollectorInterface $collector, $toolbar = null, $panel = null, $priority = 0)
    {
        $this->views[$collector->getName()] = compact('toolbar', 'panel');
        $this->order[$collector->getName()] = $priority;

        parent::add($collector);
    }

    /**
     * Returns path to toolbar view.
     *
     * @param  string $name
     * @return string|null
     */
    public function getToolbarView($name)
    {
        return isset($this->views[$name]) ? $this->views[$name]['toolbar'] : null;
    }

    /**
     * Returns path to panel view.
     *
     * @param  string $name
     * @return string|null
     */
    public function getPanelView($name)
    {
        return isset($this->views[$name]) ? $this->views[$name]['panel'] : null;
    }

    /**
     * Gets the Collectors ordered by priority.
     *
     * @return array
     */
    public function all()
    {
        arsort($this->order);
        $collectors = [];

        foreach ($this->order as $name => $priority) {
            $collectors[$name] = parent::get($name);
        }

        return $collectors;
    }
}
