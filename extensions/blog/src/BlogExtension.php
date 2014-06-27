<?php

namespace Pagekit\Blog;

use Pagekit\Blog\Event\CommentListener;
use Pagekit\Blog\Event\RouteListener;
use Pagekit\Extension\Extension;
use Pagekit\Framework\Application;
use Pagekit\System\Event\LinkEvent;
use Pagekit\System\Event\LocaleEvent;
use Pagekit\System\Event\TmplEvent;

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

        $app->on('system.link', function(LinkEvent $event) {
            $event->register('Pagekit\Blog\Link\BlogLink');
            $event->register('Pagekit\Blog\Link\PostLink');
        });

        $app->on('system.init', function() use ($app) {
            $app['config']->set('app.frontpage', $app['config']->get('app.frontpage') ?: '@blog/default');
        }, 16);

        $app->on('system.locale', function(LocaleEvent $event) {
            $event->addMessages(array('post.unsaved-form' => __('You\'ve made some changes! Leaving the post without saving will discard all changes.')));
        });

        $this->config += $app['option']->get("{$this->name}:config", $this->getConfig('defaults'));
    }

    public function enable()
    {
        if ($version = $this['migrator']->run('extension://blog/migrations', $this['option']->get('blog:version'))) {
            $this['option']->set('blog:version', $version);
        }
    }

    public function uninstall()
    {
        $this['option']->remove('blog:version');
    }
}
