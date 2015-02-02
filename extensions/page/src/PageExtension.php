<?php

namespace Pagekit\Page;

use Pagekit\Application as App;
use Pagekit\Page\Event\NodeListener;
use Pagekit\Site\Event\ConfigEvent;
use Pagekit\Site\Event\TypeEvent;
use Pagekit\System\Event\LinkEvent;
use Pagekit\System\Event\LocaleEvent;
use Pagekit\System\Event\TmplEvent;
use Pagekit\System\Extension;

class PageExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function __construct(App $app, array $config)
    {
        parent::__construct($app, $config);

        $app->subscribe(new NodeListener);

        $app->on('system.link', function (LinkEvent $event) {
            $event->register('Pagekit\Page\PageLink');
        });

        $app->on('system.locale', function (LocaleEvent $event) {
            $event->addMessages(['page.unsaved-form' => __('You\'ve made some changes! Leaving the page without saving will discard all changes.')]);
        });

        $app->on('site.types', function (TypeEvent $event) {
            $event->register('page', 'Page', [
                'tmpl.edit'   => 'page.edit'
            ]);
        });

        $app->on('site.config', function (ConfigEvent $event) use ($app) {
            $app['scripts']->queue('page-controllers', 'extensions/page/assets/js/controllers.js', 'site-application');
        });

        $app->on('system.tmpl', function (TmplEvent $event) {
            $event->register('page.edit', 'extensions/page/views/tmpl/edit.php');
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
