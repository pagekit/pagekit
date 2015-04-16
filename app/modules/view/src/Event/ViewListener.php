<?php

namespace Pagekit\View\Event;

use Pagekit\Application as App;
use Pagekit\Event\EventSubscriberInterface;
use Pagekit\View\ViewManager;
use Symfony\Component\HttpFoundation\Response;

class ViewListener implements EventSubscriberInterface
{
    /**
     * @var ViewManager
     */
    protected $view;

    /**
     * Constructor.
     *
     * @param ViewManager $view
     */
    public function __construct(ViewManager $view)
    {
        $this->view = $view;
    }

    /**
     * Renders view layout.
     *
     * @param $event
     */
    public function onKernelView($event)
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
            $this->view->section('content', (string) $result);
            if (null !== $result = $this->view->render('layout') ) {
                $response = $result;
            }
        }

        if (isset($response)) {
            $event->setResponse(new Response($response));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function subscribe()
    {
        return [
            'kernel.view' => ['onKernelView', -5]
        ];
    }
}
