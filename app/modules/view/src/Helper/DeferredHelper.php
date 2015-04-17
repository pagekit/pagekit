<?php

namespace Pagekit\View\Helper;

use Pagekit\Application;
use Pagekit\View\ViewManager;

class DeferredHelper implements HelperInterface
{
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
     *
     * @param ViewManager $view
     * @param Application $app
     */
    public function __construct(ViewManager $view, Application $app)
    {
        $view->on('render', function ($event, $view) use ($app) {

            $name = $view->getName();

            if (isset($this->placeholder[$name])) {

                $this->deferred[$name] = $view;
                $view->setResult($this->placeholder[$name]);

                $event->stopPropagation();
            }

        }, 15);

        $app->on('kernel.response', function ($event) {

            $dispatcher = $event->getDispatcher();
            $response   = $event->getResponse();

            if (!$response) {
                return;
            }

            foreach ($this->deferred as $name => $view) {

                // TODO fix prefix
                $dispatcher->trigger("view.$name", [$view]);
                $response->setContent(str_replace($this->placeholder[$name], $view->getResult(), $response->getContent()));
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
