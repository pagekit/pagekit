<?php

namespace Pagekit\Tree\Event;

use Pagekit\Framework\Event\EventSubscriber;

class RouteListener extends EventSubscriber
{
    /**
     * Adds page alias routes.
     */
    public function onRouteCollection()
    {
        $router = $this['router'];
        foreach ($this['db.em']->getRepository('Pagekit\Tree\Entity\Page')->query()->where(['status = ?'], [1])->get() as $page) {
            if ($page->getUrl()) {
                $router->addAlias($page->getPath(), $page->getUrl());
            }
        }
    }

    /**
     * Adds cache breaker.
     */
    public function onSystemInit()
    {
        $router                           = $this['router'];
        $options                          = $router->getOptions();
        $options['tree:routes.timestamp'] = $this['option']->get('tree:routes.timestamp');
        $router->setOptions($options);
    }

    /**
     * Adds cache breaker.
     */
    public function clearCache()
    {
        $this['option']->set('tree:routes.timestamp', time());
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'system.init'     => ['onSystemInit', 10],
            'route.collection' => ['onRouteCollection', 10],
            'tree.page.postSave'   => 'clearCache',
            'tree.page.postDelete' => 'clearCache'
        ];
    }
}
