<?php

namespace Pagekit\Blog\Event;

use Pagekit\Blog\UrlResolver;
use Pagekit\Routing\Event\RouteCollectionEvent;
use Pagekit\Framework\Application as App;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RouteListener implements EventSubscriberInterface
{
    protected $permalink;

    /**
     * Adds cache breaker to router.
     */
    public function onSystemInit()
    {
        App::router()->setOption('blog.permalink', $this->getPermalink());
    }

    /**
     * Register alias routes
     */
    public function onRouteCollection(RouteCollectionEvent $event)
    {
        if (!$route = $event->getRoutes()->get('@blog/id')) {
            return;
        }

        App::aliases()->add(dirname($route->getPath()).'/'.ltrim($this->getPermalink(), '/'), '@blog/id', ['_resolver' => 'Pagekit\Blog\UrlResolver']);
    }

    /**
     * Clears resolver cache
     */
    public function clearCache()
    {
        App::cache()->delete(UrlResolver::CACHE_KEY);
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

            $extension       = App::extensions()->get('blog');
            $this->permalink = $extension->getParams('permalink', '');

            if ($this->permalink === 'custom') {
                $this->permalink = $extension->getParams('permalink.custom');
            }
        }

        return $this->permalink;
    }
}
