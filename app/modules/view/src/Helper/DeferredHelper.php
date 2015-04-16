<?php

namespace Pagekit\View\Helper;

use Pagekit\Application;
use Pagekit\View\ViewManager;

class DeferredHelper implements HelperInterface
{
    /**
     * @var array
     */
    protected $defer = [];

    /**
     * Constructor.
     *
     * @param ViewManager $view
     * @param Application $app
     */
    public function __construct(ViewManager $view, Application $app)
    {
        $view->on('render', function ($event, $view) use ($app) {

            $name = $view->getName();

            if (isset($this->defer[$name])) {

                $dispatcher  = $event->getDispatcher();
                $placeholder = sprintf('<!-- %s -->', uniqid());

                $app->on('kernel.response', function ($event) use ($view, $name, $placeholder, $dispatcher) {

                    // TODO fix prefix
                    $dispatcher->trigger("view.$name", [$view]);

                    $response = $event->getResponse();
                    $response->setContent(str_replace($placeholder, $view->getResult(), $response->getContent()));

                }, 10);

                $view->setResult($placeholder);
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
