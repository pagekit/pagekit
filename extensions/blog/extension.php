<?php

return [

    'name' => 'blog',

    'main' => 'Pagekit\\Blog\\BlogExtension',

    'autoload' => [

        'Pagekit\\Blog\\' => 'src'

    ],

    'nodes' => [

        'blog' => [
            'name' => '@blog/site',
            'label' => 'Blog',
            'controller' => 'Pagekit\\Blog\\Controller\\SiteController'
        ],
        'blog-post' => [
            'label' => 'Blog Post',
            'alias' => '@blog/id'
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

    ]

];
