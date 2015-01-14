<?php

namespace Pagekit\Tree;

use Pagekit\Extension\Extension;
use Pagekit\Application as App;
use Pagekit\System\Event\TmplEvent;
use Pagekit\Tree\Event\JsonRequestListener;
use Pagekit\Tree\Event\NodeTypeEvent;
use Pagekit\Tree\Event\RouteListener;

class TreeExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function boot(App $app)
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
        if ($version = App::migrator()->create('extensions/tree/migrations', App::option()->get('tree:version'))->run()) {
            App::option()->set('tree:version', $version);
        }
    }
}
