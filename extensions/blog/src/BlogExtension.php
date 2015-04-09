<?php

namespace Pagekit\Blog;

use Pagekit\Application as App;
use Pagekit\Blog\Content\ReadmorePlugin;
use Pagekit\Blog\Event\CommentListener;
use Pagekit\Blog\Event\RouteListener;
use Pagekit\Site\Model\MountType;
use Pagekit\Site\Model\UrlType;
use Pagekit\System\Event\LinkEvent;
use Pagekit\System\Extension;

class BlogExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function main(App $app)
    {
        $app->subscribe(
//            new RouteListener,
            new CommentListener,
            new ReadmorePlugin
        );

        $app->on('system.init', function() use ($app) {
            $site = $app['module']->get('system/site');
            $site->config['frontpage'] = $site->config['frontpage'] ?: '@blog/site';
        });

        $app->on('system.link', function(LinkEvent $event) {
            $event->register('Pagekit\Blog\Link\BlogLink');
        });

        $app->on('site.types', function ($event, $site) {
            $site->registerType(new UrlType('blog.post', 'Blog Post', '@blog/id'));
            $site->registerType(new MountType('blog', 'Blog', 'Pagekit\\Blog\\Controller\\SiteController'));
        });

        $app->on('site.sections', function ($event, $site) {
            $site->registerSection('Settings', function() {
                return App::view('blog:views/admin/site/post.php', ['posts' => App::db()->createQueryBuilder()->from('@blog_post')->execute('id, title')->fetchAll(\PDO::FETCH_KEY_PAIR)]);
            }, 'blog.post');
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
