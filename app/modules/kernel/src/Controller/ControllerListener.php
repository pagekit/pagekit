<?php

namespace Pagekit\Kernel\Controller;

use Pagekit\Event\EventSubscriberInterface;

class ControllerListener implements EventSubscriberInterface
{
    /**
     * @var ControllerResolver
     */
    protected $resolver;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Constructor.
     *
     * @param ControllerResolver $resolver
     * @param LoggerInterface    $logger
     */
    public function __construct(ControllerResolver $resolver, LoggerInterface $logger = null)
    {
        $this->resolver = $resolver;
        $this->logger   = $logger;
    }

    /**
     * Sets the controller.
     *
     * @param $event
     * @param $request
     */
    public function resolveController($event, $request)
    {
        if ($event->hasResponse() || !$controller = $this->resolver->getController($request)) {
            return;
        }

        $event->setController($controller);
    }

    /**
     * Executes the controller action and sets the response.
     *
     * @param $event
     * @param $request
     */
    public function executeController($event, $request)
    {
        if ($event->hasResponse() || !$controller = $event->getController()) {
            return;
        }

        $arguments = $this->resolver->getArguments($request, $controller);
        $response  = call_user_func_array($controller, $arguments);

        $event->setResponse($response);
    }

    /**
     * {@inheritdoc}
     */
    public function subscribe()
    {
        return [
            'kernel.response' => [
                ['resolveController', 120],
                ['executeController', 100]
            ]
        ];
    }
}
