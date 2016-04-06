<?php

namespace Pagekit\View;

use Pagekit\Event\EventDispatcherInterface;
use Pagekit\Event\EventInterface;
use Pagekit\Event\PrefixEventDispatcher;
use Pagekit\Util\ArrObject;
use Pagekit\View\Event\ViewEvent;
use Pagekit\View\Helper\HelperInterface;
use Symfony\Component\Templating\DelegatingEngine;
use Symfony\Component\Templating\EngineInterface;

class View
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
     * @var HelperInterface[]
     */
    protected $helpers = [];

    /**
     * @var array[]
     */
    protected $parameters = [];

    /**
     * Constructor.
     *
     * @param EventDispatcherInterface $events
     * @param EngineInterface          $engine
     */
    public function __construct(EventDispatcherInterface $events = null, EngineInterface $engine = null)
    {
        $this->events = $events ?: new PrefixEventDispatcher('view.');
        $this->engine = $engine ?: new DelegatingEngine();

        $this->trigger('init', [$this]);
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
     * Gets a global parameter.
     *
     * @param  string $name
     * @return mixed
     */
    public function __get($name) {
        return isset($this->globals[$name]) ? $this->globals[$name] : null;
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
    public function addHelper(HelperInterface $helper)
    {
        $this->helpers[$helper->getName()] = $helper;

        $helper->register($this);

        return $this;
    }

    /**
     * Adds multiple view helpers.
     *
     * @param  HelperInterface[] $helpers
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
        $this->events->on($event, $listener, $priority);
    }

    /**
     * Triggers an event.
     *
     * @param  string $event
     * @param  array  $arguments
     * @return EventInterface
     */
    public function trigger($event, array $arguments = [])
    {
        return $this->events->trigger($event, $arguments);
    }

    /**
     * {@inheritdoc}
     */
    public function render($name, array $parameters = [])
    {
        $event = new ViewEvent('render', $name);
        $event->setParameters(array_replace($this->globals, end($this->parameters) ?: [], $parameters));

        $this->events->trigger($event, [$this]);

        if (!$event->isPropagationStopped()) {
            $name = preg_replace('/\.php$/i', '', $name);
            $this->events->trigger($event->setName($name), [$this]);
        }

        $result = $event->getResult();
        $params = $this->parameters[] = $event->getParameters();

        if ($result === null && $this->engine->supports($event->getTemplate())) {
            $result = $this->engine->render($event->getTemplate(), $params);
        }

        array_pop($this->parameters);

        return $result;
    }
}
