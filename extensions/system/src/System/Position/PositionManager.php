<?php

namespace Pagekit\System\Position;

use Pagekit\Component\View\View;
use Pagekit\System\Widget\WidgetProvider;

class PositionManager implements \ArrayAccess, \IteratorAggregate
{
    /**
     * @var View
     */
    protected $view;

    /**
     * @var WidgetProvider
     */
    protected $provider;

    /**
     * @var PositionRendererInterface[]
     */
    protected $renderers;

    /**
     * @var \ArrayObject[]
     */
    protected $positions = array();

    /**
     * Constructor.
     *
     * @param View           $view
     * @param WidgetProvider $provider
     */
    public function __construct(View $view, WidgetProvider $provider)
    {
        $this->view     = $view;
        $this->provider = $provider;

        $this->registerRenderer('default', function($provider, $position) {

            $output = array();

            foreach ($position as $widget) {
                $output[] = '<div>'.$provider->render($widget).'</div>';
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
     * Registers a position renderer.
     *
     * @param string $name
     * @param callable|string|PositionRendererInterface $renderer
     * @throws \InvalidArgumentException
     */
    public function registerRenderer($name, $renderer)
    {
        if (is_callable($renderer)) {
            $renderer = new CallbackPositionRenderer($renderer);
        }

        if (is_string($renderer)) {
            $renderer = new LayoutPositionRenderer($this->view, $renderer);
        }

        if (!$renderer instanceof PositionRendererInterface) {
            throw new \InvalidArgumentException('Renderer has to implement the PositionRendererInterface');
        }

        $this->renderers[$name] = $renderer;
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

        if (isset($options['renderer']) && $this->renderers[$options['renderer']]) {
            $renderer = $options['renderer'];
        } else {
            $renderer = 'default';
        }

        return $this->renderers[$renderer]->render($id, $this->provider, $this->offsetGet($id), $options);
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
