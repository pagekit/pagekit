<?php

namespace Pagekit\View;

use Pagekit\Application as App;
use Pagekit\Event\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;

class ViewListener implements EventSubscriberInterface
{
    /**
     * @var View
     */
    protected $view;

    /**
     * Constructor.
     *
     * @param View $view
     */
    public function __construct(View $view)
    {
        $this->view = $view;
    }

    /**
     * Renders view layout.
     *
     * @param $event
     */
    public function onController($event, $request)
    {
        $layout = true;
        $result = $event->getControllerResult();

        if (is_array($result) && isset($result['$view'])) {

            foreach ($result as $key => $value) {
                if ($key === '$view') {

                    if (isset($value['name'])) {
                        $name = $value['name'];
                        unset($value['name']);
                    }

                    if (isset($value['layout'])) {
                        $layout = $value['layout'];
                        unset($value['layout']);
                    }

                    $this->view->meta($value);

                } elseif ($key[0] === '$') {

                    $this->view->data($key, $value);
                }
            }

            if (isset($name)) {
                $response = $result = $this->view->render($name, $result);
            }
        }

        if (!is_string($result)) {
            return;
        }

        if (is_string($layout)) {
            $this->view->map('layout', $layout);
        }

        if ($layout) {

            $this->view->section('content', (string) $result);

            if (null !== $result = $this->view->render('layout')) {
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
            'app.controller' => ['onController', 50]
        ];
    }
}
