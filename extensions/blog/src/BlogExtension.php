<?php

namespace Pagekit\Blog;

use Pagekit\Blog\Content\ReadmorePlugin;
use Pagekit\Blog\Entity\Post;
use Pagekit\Blog\Event\CommentListener;
use Pagekit\Blog\Event\RouteListener;
use Pagekit\Extension\Extension;
use Pagekit\Framework\Application;
use Pagekit\System\Event\LinkEvent;
use Pagekit\System\Event\LocaleEvent;
use Pagekit\System\Event\TmplEvent;
use Pagekit\Tree\Event\NodeEditEvent;
use Pagekit\Tree\Event\NodeTypeEvent;

class BlogExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function boot(Application $app)
    {
        parent::boot($app);

        $app['events']->addSubscriber(new RouteListener);
        $app['events']->addSubscriber(new CommentListener);
        $app['events']->addSubscriber(new ReadmorePlugin);

        $app->on('system.init', function() use ($app) {
            $app['config']->set('app.frontpage', $app['config']->get('app.frontpage') ?: '@blog/site');
        }, 10);

        $app->on('system.link', function(LinkEvent $event) {
            $event->register('Pagekit\Blog\Link\BlogLink');
        });

        $app->on('system.locale', function(LocaleEvent $event) {
            $event->addMessages(['post.unsaved-form' => __('You\'ve made some changes! Leaving the post without saving will discard all changes.')]);
        });

        $app->on('tree.types', function (NodeTypeEvent $event) {
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
            $event->register('blog.post.edit', 'extensions/blog/views/tmpl/edit.razr');
        });

        $app->on('tree.node.edit', function (NodeEditEvent $event) {
            if ($event->getNode()->getType() == 'blog.post') {
                $this['view.scripts']->queue('blog-controllers', 'extensions/blog/assets/js/controllers.js', 'tree-application');
                $event->addConfig(['data' => ['posts' => array_map(function($post) { return $post->getTitle(); }, Post::findAll())]]);
            }
        });
    }

    public function enable()
    {
        if ($version = $this['migrator']->create('extensions/blog/migrations', $this['option']->get('blog:version'))->run()) {
            $this['option']->set('blog:version', $version);
        }
    }

    public function uninstall()
    {
        $this['option']->remove('blog:version');
    }
}
