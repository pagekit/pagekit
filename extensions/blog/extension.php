<?php

return array(

    'main' => 'Pagekit\\Blog\\BlogExtension',

    'autoload' => array(

        'Pagekit\\Blog\\' => 'src'

    ),

    'resources' => array(

        'export' => array(
            'view'  => 'views',
            'asset' => 'assets'
        )

    ),

    'controllers' => 'src/Controller/*Controller.php',

    'settings' => array(

        'system'  => 'blog/admin/settings.razr'

    ),

    'menu' => array(

        'blog' => array(
            'label'  => 'Blog',
            'icon'   => 'extension://blog/extension.svg',
            'url'    => '@blog/post',
            'active' => '@blog/post*',
            'access' => 'blog: manage content || blog: manage comments'
        ),
        'blog: post list' => array(
            'label'  => 'Blog',
            'parent' => 'blog',
            'url'    => '@blog/post',
            'active' => '@blog/post*',
            'access' => 'blog: manage content'
        ),
        'blog: comment list' => array(
            'label'  => 'Comments',
            'parent' => 'blog',
            'url'    => '@blog/comment',
            'active' => '@blog/comment*',
            'access' => 'blog: manage comments'
        ),

    ),

    'permissions' => array(

        'blog: manage settings' => array(
            'title' => 'Manage settings'
        ),
        'blog: manage content' => array(
            'title' => 'Manage content'
        ),
        'blog: manage comments' => array(
            'title' => 'Manage comments'
        ),
        'blog: view comments' => array(
            'title' => 'View comments'
        ),
        'blog: post comments' => array(
            'title' => 'Post comments'
        ),
        'blog: skip comment approval' => array(
            'title' => 'Skip comment approval'
        ),
        'blog: comment approval required once' => array(
            'title' => 'Comment approval required only once'
        ),
        'blog: skip comment min idle' => array(
            'title' => 'Skip comment minimum idle time'
        )

    ),

    'defaults' => array(

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
        'permalink.custom'                => 'blog/{slug}'

    )

);
