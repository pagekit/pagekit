<?php

namespace Pagekit\Debug\DataCollector;

use DebugBar\DataCollector\DataCollectorInterface;
use Pagekit\Routing\Router;

class RoutesDataCollector implements DataCollectorInterface
{
    protected $router;
    protected $cache;
    protected $file;

    /**
     * Constructor.
     *
     * @param Router $router
     * @param string $cache
     * @param string $file
     */
    public function __construct(Router $router, $cache, $file = '%s.cache')
    {
        $this->router = $router;
        $this->cache  = $cache;
        $this->file   = $file;
    }

    /**
     * {@inheritdoc}
     */
    public function collect()
    {
        $path = sprintf($this->cache .'/'. $this->file, sha1(filemtime((new \ReflectionClass($this->router->getGenerator()))->getFileName())));

        if (!file_exists($path)) {

            $routes = [];
            foreach ($this->router->getRouteCollection() as $name => $route) {
                $routes[] = [
                    'name'       => $name,
                    'pattern'    => $route->getPattern(),
                    'methods'    => $route->getMethods(),
                    'controller' => is_string($ctrl = $route->getDefault('_controller')) ? $ctrl : 'Closure',
                ];
            }

            file_put_contents($path, '<?php return '.var_export($routes, true).';');

        } else {
            $routes = require $path;
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
