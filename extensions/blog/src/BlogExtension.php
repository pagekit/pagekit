<?php

namespace Pagekit\Blog;

use Pagekit\Blog\Event\CommentListener;
use Pagekit\Blog\Event\RouteListener;
use Pagekit\Extension\Extension;
use Pagekit\Framework\Application;
use Pagekit\System\Event\LinkEvent;
use Pagekit\System\Event\LocaleEvent;

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
        });

        $app->on('system.init', function() use ($app) {

            $this->config += $app['option']->get("{$this->name}:config", array());

            $app['config']->set('app.frontpage', $app['config']->get('app.frontpage') ?: '@blog/default');

        }, 16);

        $app->on('system.locale', function(LocaleEvent $event) {
            $event->addMessages(array('post.unsaved-form' => __('You\'ve made some changes! Leaving the post without saving will discard all changes.')));
        });
    }

    public function enable()
    {
        if ($version = $this['migrator']->get('extension://blog/migrations', $this['option']->get('blog:version'))->up()) {
            $this['option']->set('blog:version', $version);
        }

        if (!$config = $this['option']->get("{$this->name}:config")) {
            $this['option']->set("{$this->name}:config", $this->getConfig('defaults'));
        }
    }

    public function uninstall()
    {
        $this['option']->remove('blog:version');
    }
}
