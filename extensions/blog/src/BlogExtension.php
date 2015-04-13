<?php

namespace Pagekit\Blog;

use Pagekit\Application as App;
use Pagekit\Blog\Content\ReadmorePlugin;
use Pagekit\Blog\Event\CommentListener;
use Pagekit\Blog\Event\RouteListener;
use Pagekit\Site\Model\MountType;
use Pagekit\Site\Model\UrlType;
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
            $site = $app['module']->get('system/site');
            $site->config['frontpage'] = $site->config['frontpage'] ?: '@blog/site';
        });

        $app->on('site.types', function ($event, $site) {
            $site->registerType(new UrlType('blog.post', 'Blog Post', '@blog/id'));
            $site->registerType(new MountType('blog', 'Blog', 'Pagekit\\Blog\\Controller\\SiteController', '@blog/site'));
        });

        $app->on('site.sections', function ($event, $site) {
            $site->registerSection('Settings', function() {
                return App::view('blog:views/admin/site/post.php', ['posts' => App::db()->createQueryBuilder()->from('@blog_post')->execute('id, title')->fetchAll(\PDO::FETCH_KEY_PAIR)]);
            }, 'blog.post');
        });
    }

    public function enable()
    {
        if ($version = App::migrator()->create('extensions/blog/migrations', App::config('blog:version'))->run()) {
            App::config()->set('blog:version', $version);
        }
    }

    public function uninstall()
    {
        App::config()->remove('blog:version');
    }

    public function getPermalink()
    {
        $permalink = $this->config('permalink.type');

        if ($permalink == 'custom') {
            $permalink = $this->config('permalink.custom');
        }

        return $permalink;
    }
}
