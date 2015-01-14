<?php

namespace Pagekit\Tree;

use Pagekit\Extension\Extension;
use Pagekit\Framework\Application;
use Pagekit\System\Event\TmplEvent;
use Pagekit\Tree\Event\JsonRequestListener;
use Pagekit\Tree\Event\NodeTypeEvent;
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

        $app['tree.types'] = function($app) {
            return $app['events']->dispatch('tree.types', new NodeTypeEvent);
        };

        $app->on('tree.types', function (NodeTypeEvent $event) {
            $event->register('alias', 'Alias', [
                'type'        => 'url',
                'tmpl.edit'   => 'alias.edit'
            ]);
        });

        $app->on('system.tmpl', function (TmplEvent $event) {
            $event->register('alias.edit', 'extensions/tree/views/tmpl/alias.razr');
        });
    }

    /**
     * {@inheritdoc}
     */
    public function enable()
    {
        if ($version = $this['migrator']->create('extensions/tree/migrations', $this['option']->get('tree:version'))->run()) {
            $this['option']->set('tree:version', $version);
        }
    }
}
