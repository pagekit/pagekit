<?php

namespace Pagekit\View;

use Pagekit\Event\EventDispatcher;
use Pagekit\Event\EventDispatcherInterface;
use Pagekit\View\Helper\HelperInterface;
use Symfony\Component\Templating\DelegatingEngine;
use Symfony\Component\Templating\EngineInterface;

class ViewManager
{
    /**
     * @var EventDispatcherInterface
     */
    protected $events;

    /**
     * @var EngineInterface
     */
    protected $engine;

    /**
     * @var array
     */
    protected $globals = [];

    /**
     * @var array
     */
    protected $helpers = [];

    /**
     * @var string
     */
    protected $prefix = 'view.';

    /**
     * Constructor.
     *
     * @param EventDispatcherInterface $events
     * @param EngineInterface          $engine
     */
    public function __construct(EventDispatcherInterface $events = null, EngineInterface $engine = null)
    {
        $this->events = $events ?: new EventDispatcher();
        $this->engine = $engine ?: new DelegatingEngine();
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

    /**
     * Gets the templating engine.
     *
     * @return array
     */
    public function getEngine()
    {
        return $this->engine;
    }

    /**
     * Adds a templating engine.
     *
     * @param  EngineInterface $engine
     * @return self
     */
    public function addEngine(EngineInterface $engine)
    {
        $this->engine->addEngine($engine);

        return $this;
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
     * @param  string $name
     * @param  mixed  $value
     * @return self
     */
    public function addGlobal($name, $value)
    {
        $this->globals[$name] = $value;

        return $this;
    }

    /**
     * Adds a view helper.
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
     * Adds multiple view helpers.
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
        $this->events->on($this->prefix.$event, $listener, $priority);
    }

    /**
     * {@inheritdoc}
     */
    public function render($name, array $parameters = [])
    {
        $view  = new View($name, array_replace($this->globals, $parameters));
        $event = $this->events->trigger($this->prefix.'render', [$view, $this]);

        if (!$event->isPropagationStopped()) {
            $this->events->trigger($this->prefix.$name, [$view, $this]);
        }

        if ($view->getResult() === null && $this->engine->supports($view->getName())) {
            return $this->engine->render($view->getName(), $view->getParameters());
        }

        return $view->getResult();
    }
}
