<?php

namespace Pagekit\Debug\DataCollector;

use DebugBar\DataCollector\DataCollectorInterface;
use Pagekit\Event\EventDispatcherInterface;
use Pagekit\Routing\Router;

class RoutesDataCollector implements DataCollectorInterface
{
    protected $router;
    protected $route;
    protected $cache;
    protected $file;

    /**
     * Constructor.
     *
     * @param Router                   $router
     * @param EventDispatcherInterface $events
     * @param string                   $cache
     * @param string                   $file
     */
    public function __construct(Router $router, EventDispatcherInterface $events, $cache, $file = '%s.cache')
    {
        $this->router = $router;
        $this->cache = $cache;
        $this->file = $file;

        $events->on('request', function ($event, $request) {
            $this->route = $request->attributes->get('_route');
        });
    }

    /**
     * {@inheritdoc}
     */
    public function collect()
    {
        $path = sprintf($this->cache.'/'.$this->file, sha1(filemtime((new \ReflectionClass($this->router->getGenerator()))->getFileName())));

        if (!file_exists($path)) {

            $routes = [];
            foreach ($this->router->getRouteCollection() as $name => $route) {
                $routes[] = [
                    'name' => $name,
                    'path' => $route->getPath(),
                    'methods' => $route->getMethods(),
                    'controller' => is_string($ctrl = $route->getDefault('_controller')) ? $ctrl : 'Closure',
                ];
            }

            file_put_contents($path, '<?php return '.var_export($routes, true).';');

        } else {
            $routes = require $path;
        }

        $route = $this->route;

        return compact('routes', 'route');
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'routes';
    }
}
