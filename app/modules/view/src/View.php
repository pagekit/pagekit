<?php

namespace Pagekit\View;

use Pagekit\View\Event\RenderEvent;
use Pagekit\View\Helper\HelperInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class View implements ViewInterface
{
    /**
     * @var EventDispatcherInterface
     */
    protected $events;

    /**
     * @var string|false
     */
    protected $layout = false;

    /**
     * @var array
     */
    protected $globals = [];

    /**
     * @var array
     */
    protected $helpers = [];

    /**
     * Constructor.
     *
     * @param EventDispatcherInterface $events
     */
    public function __construct(EventDispatcherInterface $events = null)
    {
        $this->events = $events ?: new EventDispatcher();
    }

    /**
     * Render shortcut.
     *
     * @see render()
     */
    public function __invoke($name, array $parameters = [])
    {
        return $this->render($name, $parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * {@inheritdoc}
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;
    }

    /**
     * Gets the global parameters.
     *
     * @return array
     */
    public function getGlobals()
    {
        return $this->globals;
    }

    /**
     * Adds a global parameter.
     *
     * @param string $name
     * @param mixed  $value
     */
    public function addGlobal($name, $value)
    {
        $this->globals[$name] = $value;
    }

    /**
     * Adds a helper.
     *
     * @param  HelperInterface $helper
     * @return self
     */
    public function addHelper($helper)
    {
        if (!$helper instanceof HelperInterface) {
            throw new \InvalidArgumentException(sprintf('%s does not implement HelperInterface', get_class($helper)));
        }

        $this->helpers[$helper->getName()] = $helper;

        return $this;
    }

    /**
     * Adds multiple helpers.
     *
     * @param  array $helpers
     * @return self
     */
    public function addHelpers(array $helpers)
    {
        foreach ($helpers as $helper) {
            $this->addHelper($helper);
        }

        return $this;
    }

    /**
     * Adds an event listener.
     *
     * @param  string   $event
     * @param  callable $listener
     * @param  int      $priority
     */
    public function on($event, $listener, $priority = 0)
    {
        $this->events->addListener($event, $listener, $priority);
    }

    /**
     * Renders a view.
     *
     * @param  string $name
     * @param  array  $parameters
     * @return string
     */
    public function render($name, array $parameters = [])
    {
        $param = array_replace($this->globals, $parameters);
        $event = $this->events->dispatch('render', new RenderEvent($name, $param));

        if (!$event->isPropagationStopped()) {
            $event->dispatch($name);
        }

        return $event->getResult();
    }

    /**
     * Gets a helper or calls the helpers invoke method.
     *
     * @param  string $name
     * @param  array  $args
     * @return mixed
     */
    public function __call($name, $args)
    {
        if (!isset($this->helpers[$name])) {
            throw new \InvalidArgumentException(sprintf('Undefined helper "%s"', $name));
        }

        return $args ? call_user_func_array($this->helpers[$name], $args) : $this->helpers[$name];
    }
}
