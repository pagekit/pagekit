<?php

use Pagekit\Blog\BlogExtension;

return [

    'name' => 'blog',

    'main' => function ($app, $config) {

        $extension = new BlogExtension();
        $extension->setConfig($config);
        $extension->load($app, $config);

        return $extension;
    },

    'autoload' => [

        'Pagekit\\Blog\\' => 'src'

    ],

    'resources' => [

        'export' => [
            'view'  => 'views',
            'asset' => 'assets'
        ]

    ],

    'controllers' => [

        '@blog: /blog' => [
            'Pagekit\\Blog\\Controller\\CommentController',
            'Pagekit\\Blog\\Controller\\PostController'
        ]
    ],

    'parameters' => [

        'settings' => [
            'view' => 'extensions/blog/views/admin/settings.razr',
            'defaults' => [
                'comments.autoclose'              => false,
                'comments.autoclose.days'         => 14,
                'comments.blacklist'              => '',
                'comments.comments_per_page'      => 20,
                'comments.gravatar'               => true,
                'comments.max_depth'              => 3,
                'comments.maxlinks'               => 2,
                'comments.minidle'                => 120,
                'comments.nested'                 => true,
                'comments.notifications'          => 'always',
                'comments.order'                  => 'ASC',
                'comments.replymail'              => true,
                'comments.require_name_and_email' => true,
                'posts.posts_per_page'            => 20,
                'posts.comments_enabled'          => true,
                'posts.markdown_enabled'          => true,
                'posts.show_title'                => true,
                'permalink'                       => '',
                'permalink.custom'                => '{slug}',
                'feed.type'                       => 'rss2',
                'feed.limit'                      => 20
            ]
        ]

    ],

    'menu' => [

        'blog' => [
            'label'  => 'Blog',
            'icon'   => 'extensions/blog/extension.svg',
            'url'    => '@blog/post',
            'active' => '@blog/post*',
            'access' => 'blog: manage content || blog: manage comments'
        ],
        'blog: post list' => [
            'label'  => 'Blog',
            'parent' => 'blog',
            'url'    => '@blog/post',
            'active' => '@blog/post*',
            'access' => 'blog: manage content'
        ],
        'blog: comment list' => [
            'label'  => 'Comments',
            'parent' => 'blog',
            'url'    => '@blog/comment',
            'active' => '@blog/comment*',
            'access' => 'blog: manage comments'
        ],

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

    ]

];
