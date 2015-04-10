<?php

namespace Pagekit\View\Helper;

use Pagekit\Application;
use Pagekit\View\ViewInterface;

class DeferredHelper implements HelperInterface
{
    /**
     * @var array
     */
    protected $defer = [];

    /**
     * Constructor.
     *
     * @param ViewInterface $view
     * @param Application   $app
     */
    public function __construct(ViewInterface $view, Application $app)
    {
        $view->on('render', function ($event) use ($app) {

            $template = $event->getTemplate();

            if (isset($this->defer[$template])) {

                $renderEvent = clone $event;
                $placeholder = sprintf('<!-- %s -->', uniqid());

                $app->on('kernel.response', function ($event) use ($renderEvent, $template, $placeholder) {

                    $response = $event->getResponse();
                    // TODO: fix prefix
                    $response->setContent(str_replace($placeholder, $renderEvent->dispatch('view.'.$template)->getResult(), $response->getContent()));

                }, 10);

                $event->setResult($placeholder);
                $event->stopPropagation();
            }

        }, 15);
    }

    /**
     * Defers a template render call.
     *
     * @return string
     */
    public function __invoke($name)
    {
        $this->defer[$name] = true;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'defer';
    }
}
