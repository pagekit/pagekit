<?php

namespace Pagekit\Blog\Event;

use Pagekit\Blog\UrlResolver;
use Pagekit\Framework\Event\EventSubscriber;

class RouteListener extends EventSubscriber
{
    /**
     * Register alias routes
     */
    public function onRouteCollection()
    {
        $extension = $this['extensions']->get('blog');

        if (!$permalink = $extension->getParams('permalink')) {
            return;
        }

        if ($permalink == 'custom') {
            $permalink = $extension->getParams('permalink.custom');
        }

        if (!$page = $this['db.em']->getRepository('Pagekit\Tree\Entity\Page')->query()->where(['mount = ?'], ['blog'])->first()) {
            return;
        }

        $this['router']->addAlias($page->getPath().'/'.ltrim($permalink, '/'), '@blog/id', 'Pagekit\Blog\UrlResolver');
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
            'route.collection'     => 'onRouteCollection',
            'blog.post.postSave'   => 'clearCache',
            'blog.post.postDelete' => 'clearCache'
        ];
    }
}
