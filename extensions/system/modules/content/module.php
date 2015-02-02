<?php

use Pagekit\Content\ContentHelper;
use Pagekit\Content\Plugin\MarkdownPlugin;
use Pagekit\Content\Plugin\SimplePlugin;
use Pagekit\Content\Plugin\VideoPlugin;

return [

    'name' => 'system/content',

    'main' => function ($app) {

        $app->subscribe(
            new MarkdownPlugin,
            new SimplePlugin,
            new VideoPlugin
        );

        $app['content'] = function() {
            return new ContentHelper;
        };

    },

    'autoload' => [

        'Pagekit\\Content\\' => 'src'

    ]

];
