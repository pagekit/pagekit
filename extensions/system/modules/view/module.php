<?php

use Pagekit\View\ViewListener;
use Pagekit\View\Helper\DateHelper;
use Pagekit\View\Helper\FinderHelper;
use Pagekit\View\Helper\GravatarHelper;
use Pagekit\View\Helper\MarkdownHelper;
use Pagekit\View\Helper\TokenHelper;

return [

    'name' => 'system/view',

    'main' => function ($app) {

        $app->extend('view', function($view, $app) {

            $view->addHelpers([
                'date'     => new DateHelper($app['dates']),
                'finder'   => new FinderHelper(),
                'gravatar' => new GravatarHelper(),
                'markdown' => new MarkdownHelper($app['markdown']),
                'token'    => new TokenHelper($app['csrf'])
            ]);

            return $view;
        });

        $app->subscribe(new ViewListener);
    },

    'autoload' => [

        'Pagekit\\View\\' => 'src'

    ]

];
