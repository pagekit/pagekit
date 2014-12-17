<?php

namespace Pagekit\Tree;

use Pagekit\Extension\Extension;
use Pagekit\Framework\Application;
use Pagekit\Tree\Event\JsonRequestListener;
use Pagekit\Tree\Event\MountEvent;
use Pagekit\Tree\Event\RouteListener;

class TreeExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function boot(Application $app)
    {
        parent::boot($app);

        $app['events']->addSubscriber(new JsonRequestListener);
        $app['events']->addSubscriber(new RouteListener);

        $this['mounts'] = function ($app){
            return $app['events']->dispatch('tree.mount', new MountEvent)->getMountPoints();
        };
    }

    /**
     * {@inheritdoc}
     */
    public function enable()
    {
        if ($version = $this['migrator']->create('extension://tree/migrations', $this['option']->get('tree:version'))->run()) {
            $this['option']->set('tree:version', $version);
        }
    }
}
