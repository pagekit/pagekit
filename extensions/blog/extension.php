<?php

use Pagekit\Blog\Content\ReadmorePlugin;
use Pagekit\Blog\Event\CommentListener;
use Pagekit\Blog\Event\RouteListener;

return [

    'name' => 'blog',

    'type' => 'extension',

    'main' => 'Pagekit\\Blog\\BlogExtension',

    'autoload' => [

        'Pagekit\\Blog\\' => 'src'

    ],

    'nodes' => [

        'blog' => [
            'name' => '@blog',
            'label' => 'Blog',
            'controller' => 'Pagekit\\Blog\\Controller\\SiteController',
            'protected' => true,
            'frontpage' => true
        ]

    ],

    'routes' => [

        '/blog' => [
            'name' => '@blog',
            'controller' => 'Pagekit\\Blog\\Controller\\BlogController'
        ],
        '/api/blog' => [
            'name' => '@blog/api',
            'controller' => [
                'Pagekit\\Blog\\Controller\\PostApiController',
                'Pagekit\\Blog\\Controller\\CommentApiController'
            ]
        ]

    ],

    'resources' => [

        'blog:' => ''

    ],

    'permissions' => [

        'blog: manage settings' => [
            'title' => 'Manage settings'
        ],
        'blog: manage content' => [
            'title' => 'Manage content'
        ],
        'blog: manage comments' => [
            'title' => 'Manage comments'
        ],
        'blog: view comments' => [
            'title' => 'View comments'
        ],
        'blog: post comments' => [
            'title' => 'Post comments'
        ],
        'blog: skip comment approval' => [
            'title' => 'Skip comment approval'
        ],
        'blog: comment approval required once' => [
            'title' => 'Comment approval required only once'
        ],
        'blog: skip comment min idle' => [
            'title' => 'Skip comment minimum idle time'
        ]

    ],

    'menu' => [

        'blog' => [
            'label' => 'Blog',
            'icon' => 'extensions/blog/extension.svg',
            'url' => '@blog/post',
            'active' => '@blog/post*',
            'access' => 'blog: manage content || blog: manage comments',
            'priority' => 110
        ],
        'blog: posts' => [
            'label' => 'Posts',
            'parent' => 'blog',
            'url' => '@blog/post',
            'active' => '@blog/post*',
            'access' => 'blog: manage content'
        ],
        'blog: comments' => [
            'label' => 'Comments',
            'parent' => 'blog',
            'url' => '@blog/comment',
            'active' => '@blog/comment*',
            'access' => 'blog: manage comments'
        ],
        'blog: settings' => [
            'label' => 'Settings',
            'parent' => 'blog',
            'url' => '@blog/settings',
            'active' => '@blog/settings*',
            'access' => 'blog: manage settings'
        ]

    ],

    'config' => [

        'comments' => [

            'autoclose' => false,
            'autoclose_days' => 14,
            'blacklist' => '',
            'comments_per_page' => 20,
            'gravatar' => true,
            'max_depth' => 3,
            'maxlinks' => 2,
            'minidle' => 120,
            'nested' => true,
            'notifications' => 'always',
            'order' => 'ASC',
            'replymail' => true,
            'require_name_and_email' => true,

        ],

        'posts' => [

            'posts_per_page' => 20,
            'comments_enabled' => true,
            'markdown_enabled' => true,
            'show_title' => true

        ],

        'permalink' => [
            'type' => '',
            'custom' => '{slug}'
        ],

        'feed' => [
            'type' => 'rss2',
            'limit' => 20
        ]

    ],

    'events' => [

        'boot' => function ($event, $app) {
            $app->subscribe(
                new RouteListener,
                new CommentListener,
                new ReadmorePlugin
            );
        },

        'view.scripts' => function ($event, $scripts) {
            $scripts->register('blog-site', 'blog:app/bundle/site.js', '~site-edit');
            $scripts->register('blog-link', 'blog:app/bundle/link.js', '~panel-link');
        },

        'enable.blog' => function () use ($app) {
            if ($version = $app['migrator']->create('blog:migrations', $this->config('version'))->run()) {
                $app['config']($this->name)->set('version', $version);
            }
        },

        'uninstall.blog' => function () use ($app) {
            $app['migrator']->create('blog:migrations', $this->config('version'))->run(0);
            $app['config']()->remove($this->name);
        }

    ]

];
