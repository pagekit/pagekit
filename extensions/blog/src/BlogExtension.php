<?php

namespace Pagekit\Blog;

use Pagekit\Application as App;
use Pagekit\Blog\Content\ReadmorePlugin;
use Pagekit\Blog\Event\CommentListener;
use Pagekit\Blog\Event\RouteListener;
use Pagekit\System\Extension;
use Pagekit\System\Event\LinkEvent;
use Pagekit\System\Event\LocaleEvent;
use Pagekit\System\Event\TmplEvent;
use Pagekit\Site\Event\ConfigEvent;
use Pagekit\Site\Event\TypeEvent;

class BlogExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(App $app, array $config)
    {
        parent::load($app, $config);

        $app->subscribe(
            new RouteListener,
            new CommentListener,
            new ReadmorePlugin
        );

        $app->on('system.init', function() use ($app) {
            $app['config']->set('app.frontpage', $app['config']->get('app.frontpage') ?: '@blog/site');
        }, 10);

        $app->on('system.link', function(LinkEvent $event) {
            $event->register('Pagekit\Blog\Link\BlogLink');
        });

        $app->on('system.locale', function(LocaleEvent $event) {
            $event->addMessages(['post.unsaved-form' => __('You\'ve made some changes! Leaving the post without saving will discard all changes.')]);
        });

        $app->on('site.types', function (TypeEvent $event) {
            $event->register('blog', 'Blog', [
                'type'        => 'mount',
                'controllers' => 'Pagekit\\Blog\\Controller\\SiteController',
                'url'         => '@blog/site'
            ]);

            $event->register('blog.post', 'Blog Post', [
                'type'      => 'url',
                'tmpl.edit' => 'blog.post.edit'
            ]);
        });

        $app->on('system.tmpl', function (TmplEvent $event) {
            $event->register('blog.post.edit', 'extensions/blog/views/tmpl/edit.php');
        });

        $app->on('site.config', function (ConfigEvent $event) use ($app) {
            $app['scripts']->queue('blog-controllers', 'extensions/blog/app/controllers.js', 'site-application');
            $event->addConfig(['data' => ['posts' => App::db()->createQueryBuilder()->from('@blog_post')->execute('id, title')->fetchAll(\PDO::FETCH_KEY_PAIR)]]);
        });
    }

    public function enable()
    {
        if ($version = App::migrator()->create('extensions/blog/migrations', App::option('blog:version'))->run()) {
            App::option()->set('blog:version', $version);
        }
    }

    public function uninstall()
    {
        App::option()->remove('blog:version');
    }
}
