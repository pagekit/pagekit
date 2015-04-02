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
     * @param string                              $name
     * @param EventDispatcherInterface            $dispatcher
     */
    public function onKernelRequest()
    {
        $app = App::getInstance();

        $this->view->on('render', function($event) use ($app) {

            $template = $event->getTemplate();

            if ($template == 'head') {

                $renderEvent = clone $event;
                $placeholder = sprintf('<!-- %s -->', uniqid());

                $app->on('kernel.response', function($event) use ($renderEvent, $template, $placeholder) {

                    $response = $event->getResponse();
                    $response->setContent(str_replace($placeholder, $renderEvent->dispatch($template)->getResult(), $response->getContent()));

                }, 10);

                $event->setResult($placeholder);
                $event->stopPropagation();
            }

        }, 15);

        $this->view->on('render', function($event) use ($app) {
            if (isset($app['locator']) and $template = $app['locator']->get($event->getTemplate())) {
                $event->setTemplate($template);
            }
        }, 10);

        $this->view->on('render', function($event) use ($app) {
            if ($app['templating']->supports($template = $event->getTemplate())) {
                $event->setResult($app['templating']->render($template, $event->getParameters()));
            }
        }, -10);
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
            'kernel.request' => 'onKernelRequest',
            'kernel.view'    => ['onKernelView', -5]
        ];
    }
}
