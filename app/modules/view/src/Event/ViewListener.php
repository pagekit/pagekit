<?php

namespace Pagekit\View\Event;

use Pagekit\View\ViewInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

class ViewListener implements EventSubscriberInterface
{
    /**
     * @var ViewInterface
     */
    protected $view;

    /**
     * Constructor.
     *
     * @param ViewInterface $view
     */
    public function __construct(ViewInterface $view)
    {
        $this->view = $view;
    }

    /**
     * Renders view layout.
     *
     * @param GetResponseForControllerResultEvent $event
     * @param string                              $name
     * @param EventDispatcherInterface            $dispatcher
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $request = $event->getRequest();
        $result  = $event->getControllerResult();

        if (null !== $template = $request->attributes->get('_response[value]', null, true) and (null === $result || is_array($result))) {
            $response = $result = $this->view->render($template, $result ?: []);
        }

        if ($layout = $request->attributes->get('_response[layout]', null, true) or (null === $layout && $layout = $this->view->getLayout())) {

            $event->getDispatcher()->dispatch('view.layout', $e = new LayoutEvent($layout));
            $this->view->section()->set('content', (string) $result);

            $response = $this->view->render($e->getLayout(), $e->getParameters());
        }

        if (isset($response)) {
            $event->setResponse(new Response($response));
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'kernel.view' => ['onKernelView', -5]
        ];
    }
}
