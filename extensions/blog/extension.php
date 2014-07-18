<?php

return [

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

    'controllers' => 'src/Controller/*Controller.php',

    'settings' => [

        'system'  => 'extension://blog/views/admin/settings.razr'

    ],

    'menu' => [

        'blog' => [
            'label'  => 'Blog',
            'icon'   => 'extension://blog/extension.svg',
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

    ],

    'defaults' => [

        'comments.autoclose'              => false,
        'comments.autoclose.days'         => 14,
        'comments.blacklist'              => '',
        'comments.comments_per_page'      => 20,
        'comments.enabled'                => true,
        'comments.gravatar'               => true,
        'comments.max_depth'              => 3,
        'comments.maxlinks'               => 2,
        'comments.minidle'                => 120,
        'comments.nested'                 => true,
        'comments.notifications'          => 'always',
        'comments.order'                  => 'ASC',
        'comments.replymail'              => true,
        'comments.require_name_and_email' => true,
        'permalink'                       => '',
        'permalink.custom'                => 'blog/{slug}',
        'feed.type'                       => 'rss2',
        'feed.limit'                      => 20

    ]

];
