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
     * @param ViewManager $manager
     * @param Application $app
     */
    public function __construct(ViewManager $manager, Application $app)
    {
        $manager->on('render', function ($event, $view) use ($app) {

            $name = $view->getName();

            if (isset($this->placeholder[$name])) {

                $this->deferred[$name] = $view;
                $view->setResult($this->placeholder[$name]);

                $event->stopPropagation();
            }

        }, 15);

        $app->on('app.response', function ($event, $request, $response) use ($manager) {

            $dispatcher = $event->getDispatcher();

            foreach ($this->deferred as $name => $view) {

                $view->setResult('');

                // TODO fix prefix
                $dispatcher->trigger("view.$name", [$view, $manager]);
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
