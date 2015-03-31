<?php

return [

    'name' => 'blog',

    'main' => 'Pagekit\\Blog\\BlogExtension',

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
        ],

        '@blog/api: /api/blog' => [
            'Pagekit\\Blog\\Controller\\PostApiController',
            'Pagekit\\Blog\\Controller\\CommentApiController'
        ]
    ],

    'config' => [

        'settings.view' => 'blog:views/admin/settings.razr',

        'comments'      => [

            'autoclose'              => false,
            'autoclose.days'         => 14,
            'blacklist'              => '',
            'comments_per_page'      => 20,
            'gravatar'               => true,
            'max_depth'              => 3,
            'maxlinks'               => 2,
            'minidle'                => 120,
            'nested'                 => true,
            'notifications'          => 'always',
            'order'                  => 'ASC',
            'replymail'              => true,
            'require_name_and_email' => true,

        ],

        'posts'         => [

            'posts_per_page'   => 20,
            'comments_enabled' => true,
            'markdown_enabled' => true,
            'show_title'       => true

        ],

        'permalink'     => [
            'type'   => '',
            'custom' => '{slug}'
        ],

        'feed'          => [
            'type'  => 'rss2',
            'limit' => 20
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
            'label'  => 'Posts',
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

    ],

    'templates' => [

        'blog.post.edit' => 'blog:views/tmpl/edit.php'

    ]

];
