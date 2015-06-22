<?php

namespace Pagekit\Blog\Event;

use Pagekit\Application as App;
use Pagekit\Blog\UrlResolver;
use Pagekit\Event\EventSubscriberInterface;

class RouteListener implements EventSubscriberInterface
{
    protected $permalink;

    /**
     * Adds cache breaker to router.
     */
    public function onAppRequest()
    {
        App::router()->setOption('blog.permalink', $this->permalink = App::module('blog')->getPermalink());
    }

    /**
     * Registers permalink route alias.
     */
    public function onConfigureRoute($event, $route)
    {
        if ($route->getName() == '@blog/id' && $this->permalink) {
            App::routes()->alias(dirname($route->getPath()).'/'.ltrim($this->permalink, '/'), '@blog/id', ['_resolver' => 'Pagekit\Blog\UrlResolver']);
        }
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
            'request'          => ['onAppRequest', 130],
            'route.configure'      => 'onConfigureRoute',
            'blog.post.postSave'   => 'clearCache',
            'blog.post.postDelete' => 'clearCache'
        ];
    }
}
