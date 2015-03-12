<?php

namespace Pagekit\Blog;

use Pagekit\Application as App;
use Pagekit\Blog\Content\ReadmorePlugin;
use Pagekit\Blog\Event\CommentListener;
use Pagekit\Blog\Event\RouteListener;
use Pagekit\Site\Event\ConfigEvent;
use Pagekit\System\Event\LinkEvent;
use Pagekit\System\Event\TmplEvent;
use Pagekit\System\Extension;

class BlogExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function main(App $app)
    {
        $app->subscribe(
            new RouteListener,
            new CommentListener,
            new ReadmorePlugin
        );

        $app->on('system.init', function() use ($app) {
            $app['system']->config['frontpage'] = $app['system']->config['frontpage'] ?: '@blog/site';
        }, 10);

        $app->on('system.loaded', function () use ($app) {
            $app['view']->tmpl()->register('blog.post.edit', 'extensions/blog/views/tmpl/edit.php');
        });

        $app->on('system.link', function(LinkEvent $event) {
            $event->register('Pagekit\Blog\Link\BlogLink');
        });

        $app->on('site.types', function ($event, $site) {

            $site->registerType('blog', 'Blog', [
                'type'        => 'mount',
                'controllers' => 'Pagekit\\Blog\\Controller\\SiteController',
                'url'         => '@blog/site'
            ]);

            $site->registerType('blog.post', 'Blog Post', [
                'type'      => 'url',
                'tmpl.edit' => 'blog.post.edit'
            ]);

        });

        // TODO fix
        $app->on('site.config', function (ConfigEvent $event) use ($app) {
            $app['scripts']->add('blog-controllers', 'extensions/blog/app/controllers.js', 'site-application');
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
