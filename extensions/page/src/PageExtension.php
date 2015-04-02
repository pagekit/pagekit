<?php

namespace Pagekit\Page;

use Pagekit\Application as App;
use Pagekit\Page\Event\NodeListener;
use Pagekit\System\Event\LinkEvent;
use Pagekit\System\Extension;

class PageExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function main(App $app)
    {
        $app->subscribe(new NodeListener);

        $app->on('system.link', function (LinkEvent $event) {
            $event->register('Pagekit\Page\PageLink');
        });

        $app->on('site.types', function ($event, $site) {
            $site->registerType('page', 'Page', 'url');
        });

        $app->on('site.config', function () use ($app) {
            $app['scripts']->add('page-controllers', 'extensions/page/assets/js/controllers.js', 'site-application');
        });

        $app->on('site.sections', function ($event, $site) {
            $site->registerSection('Settings', function() {
                return App::view('blog:views/admin/site.post.php', ['posts' => App::db()->createQueryBuilder()->from('@blog_post')->execute('id, title')->fetchAll(\PDO::FETCH_KEY_PAIR)]);
            }, 'blog.post');
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
