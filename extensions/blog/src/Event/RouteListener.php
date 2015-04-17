<?php

namespace Pagekit\Blog\Event;

use Pagekit\Application as App;
use Pagekit\Blog\UrlResolver;
use Pagekit\Event\EventSubscriberInterface;
use Pagekit\Routing\Event\RouteCollectionEvent;

class RouteListener implements EventSubscriberInterface
{
    protected $permalink;

    /**
     * Adds cache breaker to router.
     */
    public function onSystemInit()
    {
        App::router()->setOption('blog.permalink', $this->permalink = App::module('blog')->getPermalink());
    }

    /**
     * Register alias routes.
     */
    public function onRouteCollection(RouteCollectionEvent $event)
    {
        if (!$this->permalink or !$route = $event->getRoutes()->get('@blog/id')) {
            return;
        }

        App::aliases()->add(dirname($route->getPath()).'/'.ltrim($this->permalink, '/'), '@blog/id', ['_resolver' => 'Pagekit\Blog\UrlResolver']);
    }

    /**
     * Clears resolver cache.
     */
    public function clearCache()
    {
        App::cache()->delete(UrlResolver::CACHE_KEY);
    }

    /**
     * {@inheritdoc}
     */
    public function subscribe()
    {
        return [
            'system.init'          => 'onSystemInit',
            'route.collection'     => 'onRouteCollection',
            'blog.post.postSave'   => 'clearCache',
            'blog.post.postDelete' => 'clearCache'
        ];
    }
}
