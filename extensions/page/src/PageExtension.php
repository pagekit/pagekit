<?php

namespace Pagekit\Page;

use Pagekit\Application as App;
use Pagekit\Extension\Extension;
use Pagekit\Page\Event\NodeListener;
use Pagekit\System\Event\LinkEvent;
use Pagekit\System\Event\LocaleEvent;
use Pagekit\System\Event\TmplEvent;
use Pagekit\Tree\Event\NodeEditEvent;
use Pagekit\Tree\Event\NodeTypeEvent;

class PageExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function boot(App $app)
    {
        parent::boot($app);

        $app->subscribe(new NodeListener);

        $app->on('system.link', function (LinkEvent $event) {
            $event->register('Pagekit\Page\PageLink');
        });

        $app->on('system.locale', function (LocaleEvent $event) {
            $event->addMessages(['page.unsaved-form' => __('You\'ve made some changes! Leaving the page without saving will discard all changes.')]);
        });

        $app->on('tree.types', function (NodeTypeEvent $event) {
            $event->register('page', 'Page', [
                'tmpl.edit'   => 'page.edit'
            ]);
        });

        $app->on('tree.node.edit', function (NodeEditEvent $event) use ($app) {
            if ($event->getNode()->getType() == 'page') {
                $app['scripts']->queue('page-controllers', 'extensions/page/assets/js/controllers.js', 'tree-application');
            }
        });

        $app->on('system.tmpl', function (TmplEvent $event) {
            $event->register('page.edit', 'extensions/page/views/tmpl/edit.razr');
        });
    }

    /**
     * {@inheritdoc}
     */
    public function enable()
    {
        if ($version = App::migrator()->create('extensions/page/migrations', App::option('page:version'))->run()) {
            App::option()->set('page:version', $version);
        }
    }
}
