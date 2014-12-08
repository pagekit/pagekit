<?php

namespace Pagekit\Tree\Event;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Annotations\SimpleAnnotationReader;
use Pagekit\Component\Database\ORM\Repository;
use Pagekit\Component\Routing\Event\ConfigureRouteEvent;
use Pagekit\Framework\Event\EventSubscriber;
use Pagekit\Tree\Annotation\Route;
use Pagekit\Tree\Entity\Page;
use Symfony\Component\Routing\RouteCollection;


class MountListener extends EventSubscriber
{
    /**
     * @var Reader
     */
    protected $reader;

    /**
     * @var Repository
     */
    protected $pages;

    /**
     * @var array
     */
    protected $mounts;

    /**
     * @var RouteCollection
     */
    protected $routes;

    /**
     * Constructor.
     *
     * @param Reader $reader
     */
    public function __construct(Reader $reader = null)
    {
        $this->reader = $reader;

        $this['mounts'] = function ($app){
            return $app['events']->dispatch('tree.mount', new MountEvent)->getMountPoints();
        };

        $this->routes = new RouteCollection;
    }

    /**
     * Collects the mounted routes.
     *
     * @param ConfigureRouteEvent $event
     */
    public function onConfigureRoute(ConfigureRouteEvent $event)
    {
        $pages = $this->getPages();

        if (!$this->reader) {
            $this->reader = new SimpleAnnotationReader;
            $this->reader->addNamespace('Pagekit\Tree\Annotation');
        }

        if ($annot = $this->reader->getClassAnnotation($event->getClass(), 'Pagekit\Tree\Annotation\Route') and $annot->getMount()) {
            $mount = $annot->getMount();
        }

        foreach ($this->reader->getMethodAnnotations($event->getMethod()) as $annot) {
            if ($annot instanceof Route && $annot->getMount()) {
                $mount = $annot->getMount();
            }
        }

        if (!empty($mount) && isset($this['mounts'][$mount])) {

            $route   = $event->getRoute();
            $options = $event->getOptions();
            $path    = $route->getPath();
            $name    = $options['name'];

            $event->setRoute(null);

            if (0 !== strpos($path, $options['path'])) {
                return;
            }

            $index = 1;
            foreach(array_filter($pages, function($page) use ($mount) { return $page->getMount() == $mount; }) as $page) {

                $copy  = clone($route);
                $copy->setPath(rtrim($page->getPath().substr($path, strlen($options['path']))), '/');

                $this->routes->add(sprintf('%s_mounted_%s', $name, $index), $copy);
                $index++;
            }
        }
    }

    /**
     * Initialize mount points.
     */
    public function onSystemInit()
    {
        $router            = $this['router'];
        $options           = $router->getOptions();
        $options['mounts'] = $this['mounts'];
        $router->setOptions($options);
    }

    /**
     * Adds the mounted routes.
     */
    public function onRouteCollection($event)
    {
        $event['routes']->addCollection($this->routes);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'route.configure'  => 'onConfigureRoute',
            'route.collection' => 'onRouteCollection',
            'system.init'      => ['onSystemInit', 10]
        ];
    }

    /**
     * @return Page[]
     */
    protected function getPages()
    {
        if (!$this->pages) {
            $this->pages = $this['db.em']->getRepository('Pagekit\Tree\Entity\Page')->findAll();
        }

        return $this->pages;
    }
}
