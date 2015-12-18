<?php

namespace Pagekit\View\Helper;

use Pagekit\Event\EventDispatcherInterface;
use Pagekit\View\View;

class DeferredHelper implements HelperInterface
{
    /**
     * @var EventDispatcherInterface
     */
    protected $events;

    /**
     * @var array
     */
    protected $deferred = [];

    /**
     * @var array
     */
    protected $placeholder = [];

    /**
     * Constructor.

     * @param EventDispatcherInterface $events
     */
    public function __construct(EventDispatcherInterface $events)
    {
        $this->events = $events;
    }

    /**
     * {@inheritdoc}
     */
    public function register(View $view)
    {
        $view->on('render', function ($event) {

            $name = $event->getTemplate();

            if (isset($this->placeholder[$name])) {

                $this->deferred[$name] = clone $event;

                $event->setResult($this->placeholder[$name]);
                $event->stopPropagation();
            }

        }, 15);

        $this->events->on('response', function ($e, $request, $response) use ($view) {

            foreach ($this->deferred as $name => $event) {
                $view->trigger($event->setName($name), [$view]);
                $response->setContent(str_replace($this->placeholder[$name], $event->getResult(), $response->getContent()));
            }

        }, 10);
    }

    /**
     * Defers a template render call.
     *
     * @return string
     */
    public function __invoke($name)
    {
        $this->placeholder[$name] = sprintf('<!-- %s -->', uniqid());
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'defer';
    }
}
