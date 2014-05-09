<?php

namespace Pagekit\Widget;

use Pagekit\Framework\ApplicationAware;
use Pagekit\Widget\Event\RegisterRendererEvent;

class PositionManager extends ApplicationAware implements \ArrayAccess, \IteratorAggregate
{
    /**
     * @var WidgetProvider
     */
    protected $provider;

    /**
     * @var RegisterRendererEvent
     */
    protected $renderers;

    /**
     * @var \ArrayObject[]
     */
    protected $positions = array();

    /**
     * Constructor.
     *
     * @param WidgetProvider $provider
     */
    public function __construct(WidgetProvider $provider)
    {
        $this->provider  = $provider;
        $this->renderers = $this('events')->dispatch('system.position.renderer', new RegisterRendererEvent($this('view')));

        $this->renderers->register('default', function ($position, WidgetProvider $provider, \ArrayObject $widgets, array $options = array()) {
            $output = array();

            foreach ($widgets as $widget) {
                $output[] = '<div>'.$provider->render($widget, $options).'</div>';
            }

            return implode("\n", $output);
        });
    }

    /**
     * Check if a position is registered.
     *
     * @param  mixed $id
     * @return boolean
     */
    public function has($id)
    {
        return isset($this->positions[$id]);
    }

    /**
     * Register a position.
     *
     * @param  mixed $id
     * @throws \InvalidArgumentException
     */
    public function register($id)
    {
        if ($this->has($id)) {
            throw new \InvalidArgumentException("Position with id '$id' is already registered.");
        }

        $this->positions[$id] = new \ArrayObject;
    }

    /**
     * Unregister a position.
     *
     * @param mixed $id
     */
    public function unregister($id)
    {
        unset($this->positions[$id]);
    }

    /**
     * Counts widgets assigned to a position.
     *
     * @param  mixed $id
     * @return int
     */
    public function count($id)
    {
        return isset($this->positions[$id]) ? count($this->positions[$id]) : 0;
    }

    /**
     * Check if there are widgets assigned to a position or multiple positions using a boolean expression.
     *
     * @param  string $expression
     * @throws \InvalidArgumentException
     * @return boolean
     */
    public function exists($expression)
    {
        $self = $this;

        if (empty($expression)) {
            return true;
        }

        if (!preg_match('/[^&\(\)\|\!]/', $expression)) {
            return $this->count($expression) > 0;
        }

        $exp = preg_replace('/[^01&\(\)\|!]/', '', preg_replace_callback('/[a-z_][a-z-_\.:\d\s]*/i', function($position) use ($self) {
            return $self->count(trim($position[0])) ? 1 : 0;
        }, $expression));

        if (!$fn = @create_function("", "return $exp;")) {
            throw new \InvalidArgumentException(sprintf('Unable to parse the given expression "%s"', $expression));
        }

        return (bool) $fn();
    }

    /**
     * Renders widgets output for a position.
     *
     * @param  mixed $id
     * @param  array $options
     * @return string
     */
    public function render($id, array $options = array())
    {
        if (!$this->has($id)) {
            return;
        }

        $renderer = isset($options['renderer'], $this->renderers[$options['renderer']]) ? $options['renderer'] : 'default';

        return $this->renderers[$renderer]->render($id, $this->provider, $this[$id], $options);
    }

    /**
     * ArrayAccess for get position.
     *
     * @param  mixed $id
     * @return \ArrayObject
     */
    public function offsetGet($id)
    {
        if (!$this->has($id)) {
            $this->register($id);
        }

        return $this->positions[$id];
    }

    /**
     * ArrayAccess for register position.
     *
     * @param  mixed $id
     * @param  mixed $position
     * @throws \BadMethodCallException
     */
    public function offsetSet($id, $position)
    {
        throw new \BadMethodCallException(sprintf("Not supported, use %s::register() instead.", get_class($this)));
    }

    /**
     * ArrayAccess for unset position.
     *
     * @param mixed $id
     */
    public function offsetUnset($id)
    {
        $this->unregister($id);
    }

    /**
     * ArrayAccess for position exists.
     *
     * @param  mixed $id
     * @return boolean
     */
    public function offsetExists($id)
    {
        return $this->has($id);
    }

    /**
     * Implements the IteratorAggregate.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->positions);
    }
}
