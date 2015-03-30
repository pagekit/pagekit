<?php

namespace Pagekit\Routing\DataCollector;

use Pagekit\Routing\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

/**
 * UserDataCollector.
 */
class RoutesDataCollector extends DataCollector
{
    protected $router;
    protected $cache;
    protected $file;

    /**
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
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $path = sprintf($this->cache.'/'.$this->file, sha1(filemtime((new \ReflectionClass($this->router->getGenerator()))->getFileName())));

        if (!file_exists($path)) {

            $routes = [];
            foreach ($this->router->getRouteCollection() as $name => $route) {
                $routes[$name] = [
                    'pattern'    => $route->getPattern(),
                    'controller' => is_string($ctrl = $route->getDefault('_controller')) ? $ctrl : 'Closure',
                    'methods'    => $route->getMethods()
                ];
            }

            file_put_contents($path, '<?php return '.var_export($routes, true).';');
        }

        $this->data['path'] = $path;
    }

    public function getRoutes()
    {
        if (!isset($this->data['path']) || !file_exists($this->data['path'])) {
            return [];
        }

        return require $this->data['path'];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'routes';
    }
}
