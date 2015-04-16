<?php

namespace Pagekit\Kernel\Controller;

use Pagekit\Event\EventDispatcherInterface;
use Pagekit\Kernel\Event\GetControllerEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerResolver as BaseResolver;

class ControllerResolver extends BaseResolver
{
    protected $events;

    /**
     * Constructor.
     *
     * @param EventDispatcherInterface $events
     * @param LoggerInterface          $logger
     */
    public function __construct(EventDispatcherInterface $events, LoggerInterface $logger = null)
    {
        parent::__construct($logger);

        $this->events = $events;
    }

    /**
     * {@inheritdoc}
     */
    public function getController(Request $request)
    {
        return $this->events->trigger(new GetControllerEvent($request), [$request])->getController() ?: parent::getController($request);
    }
}
