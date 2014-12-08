<?php

namespace Pagekit\Tree\Event;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Annotations\SimpleAnnotationReader;
use Pagekit\Component\Database\ORM\Repository;
use Pagekit\Component\Routing\Event\ConfigureRouteEvent;
use Pagekit\Framework\Event\EventSubscriber;
use Pagekit\Tree\Annotation\Route;

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
     * Constructor.
     *
     * @param Reader $reader
     */
    public function __construct(Reader $reader = null)
    {
        $this->reader = $reader;
    }

    /**
     * Reads the mount points from the controller and configures the routes accordingly.
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

            if ($page = array_filter($pages, function($page) use ($mount) { return $page->getMount() == $mount; }) and 0 === strpos($path, $options['path'])) {
                $path = array_pop($page)->getPath() . substr($path, strlen($options['path']));
            }

            $route->setPath(rtrim($path), '/');
        }
    }

    /**
     * Initialize mount points.
     */
    public function onSystemInit()
    {
        $this['mounts'] = function ($app){
            return $app['events']->dispatch('tree.mount', new MountEvent)->getMountPoints();
        };

        $router            = $this['router'];
        $options           = $router->getOptions();
        $options['mounts'] = $this['mounts'];
        $router->setOptions($options);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'route.configure' => 'onConfigureRoute',
            'system.init'     => ['onSystemInit', 10]
        ];
    }

    /**
     * @return Repository
     */
    protected function getPages()
    {
        if (!$this->pages) {
            $this->pages = $this['db.em']->getRepository('Pagekit\Tree\Entity\Page')->findAll();
        }

        return $this->pages;
    }
}
