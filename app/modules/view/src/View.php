<?php

namespace Pagekit\View;

use Pagekit\View\Event\RenderEvent;
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
     * @param AssetManager $manager
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
     * Sets the helpers.
     *
     * @param  array $helpers
     * @return self
     */
    public function setHelpers(array $helpers)
    {
        $this->helpers = [];

        return $this->addHelpers($helpers);
    }

    /**
     * Adds multiple helpers.
     *
     * @param  array $helpers
     * @return self
     */
    public function addHelpers(array $helpers)
    {
        foreach ($helpers as $name => $helper) {
            $this->helpers[$name] = $helper;
        }

        return $this;
    }

    /**
     * Adds a helper.
     *
     * @param  string $name
     * @param  mixed  $helper
     * @return self
     */
    public function addHelper($name, $helper)
    {
        $this->helpers[$name] = $helper;

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
        $event  = $this->events->dispatch('view.render', new RenderEvent($name, array_replace($this->globals, $parameters)));
        $result = $this->events->dispatch($name, $event)->getResult();

        return $result;
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
