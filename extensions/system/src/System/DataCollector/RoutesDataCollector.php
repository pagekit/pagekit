<?php

namespace Pagekit\System\DataCollector;

use Pagekit\Framework\ApplicationTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

/**
 * UserDataCollector.
 */
class RoutesDataCollector extends DataCollector implements \ArrayAccess
{
    use ApplicationTrait;

    const CACHE_KEY = 'system:routes_data_collector.';

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
    }

    public function getRoutes()
    {
        $key = self::CACHE_KEY . get_class($this['router']->getGenerator());
        if (false === $routes = $this['cache']->fetch($key)) {

            $routes = [];
            foreach ($this['router']->getRouteCollection() as $name => $route) {
                $routes[$name] = ['pattern' => $route->getPattern(), 'controller' => is_string($ctrl = $route->getDefault('_controller')) ? $ctrl : 'Closure'];
            }

            $this['cache']->save($key, $routes);
        }

        return $routes;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'routes';
    }
}
