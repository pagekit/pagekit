<?php

namespace Pagekit\View\Event;

use Pagekit\Application as App;
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
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $template = $event->getRequest()->attributes->get('_response[value]', null, true);
        $layout   = $event->getRequest()->attributes->get('_response[layout]', true, true);
        $result   = $event->getControllerResult();

        if ($template !== null && ($result === null || is_array($result))) {
            $response = $result = $this->view->render($template, $result ?: []);
        }

        if (is_string($layout)) {
            $this->view->map('layout', $layout);
        }

        if ($layout) {
            $this->view->section()->set('content', (string) $result);
            $response = $this->view->render('layout');
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
