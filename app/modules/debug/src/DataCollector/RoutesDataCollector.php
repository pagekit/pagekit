<?php

namespace Pagekit\Debug\DataCollector;

use DebugBar\DataCollector\DataCollectorInterface;
use Pagekit\Routing\Router;

class RoutesDataCollector implements DataCollectorInterface
{
    protected $router;

    /**
     * Constructor.
     *
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function collect()
    {
        $routes = [];

        foreach ($this->router->getRouteCollection() as $name => $route) {
            $routes[] = [
                'name'       => $name,
                'pattern'    => $route->getPattern(),
                'methods'    => $route->getMethods(),
                'controller' => is_string($ctrl = $route->getDefault('_controller')) ? $ctrl : 'Closure',
            ];
        }

        return compact('routes');
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'routes';
    }
}
