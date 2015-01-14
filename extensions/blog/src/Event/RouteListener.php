<?php

namespace Pagekit\Blog\Event;

use Pagekit\Blog\UrlResolver;
use Pagekit\Component\Routing\Event\RouteCollectionEvent;
use Pagekit\Framework\Event\EventSubscriber;

class RouteListener extends EventSubscriber
{
    protected $permalink;

    /**
     * Adds cache breaker to router.
     */
    public function onSystemInit()
    {
        $this['router']->setOption('blog.permalink', $this->getPermalink());
    }

    /**
     * Register alias routes
     */
    public function onRouteCollection(RouteCollectionEvent $event)
    {
        if (!$route = $event->getRoutes()->get('@blog/id')) {
            return;
        }

        $this['aliases']->add(dirname($route->getPath()).'/'.ltrim($this->getPermalink(), '/'), '@blog/id', ['_resolver' => 'Pagekit\Blog\UrlResolver']);
    }

    /**
     * Clears resolver cache
     */
    public function clearCache()
    {
        $this['cache']->delete(UrlResolver::CACHE_KEY);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'system.init'          => ['onSystemInit', 10],
            'route.collection'     => 'onRouteCollection',
            'blog.post.postSave'   => 'clearCache',
            'blog.post.postDelete' => 'clearCache'
        ];
    }

    /**
     * @return string
     */
    protected function getPermalink()
    {
        if (null === $this->permalink) {

            $extension       = $this['extensions']->get('blog');
            $this->permalink = $extension->getParams('permalink', '');

            if ($this->permalink === 'custom') {
                $this->permalink = $extension->getParams('permalink.custom');
            }
        }

        return $this->permalink;
    }
}
