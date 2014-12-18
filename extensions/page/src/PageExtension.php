<?php

namespace Pagekit\Page;

use Pagekit\Extension\Extension;
use Pagekit\Framework\Application;
use Pagekit\Page\Event\AliasListener;
use Pagekit\System\Event\LinkEvent;
use Pagekit\System\Event\LocaleEvent;
use Pagekit\Tree\Event\NodeTypeEvent;

class PageExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function boot(Application $app)
    {
        parent::boot($app);

        //$app['events']->addSubscriber(new AliasListener);

        $app->on('system.link', function(LinkEvent $event) {
            $event->register('Pagekit\Page\PageLink');
        });

        $app->on('system.locale', function(LocaleEvent $event) {
            $event->addMessages(['page.unsaved-form' => __('You\'ve made some changes! Leaving the page without saving will discard all changes.')]);
        });

        $app->on('tree.types', function(NodeTypeEvent $event) {
            $event->register('page', 'Page', ['tmpl.edit' => 'page.tmpl.edit']);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function enable()
    {
        if ($version = $this['migrator']->create('extension://page/migrations', $this['option']->get('page:version'))->run()) {
            $this['option']->set('page:version', $version);
        }
    }
}
