<?php

use Pagekit\Editor\Editor;
use Pagekit\Editor\EditorHelper;
use Razr\Directive\FunctionDirective;

return [

    'name' => 'system/editor',

    'main' => function ($app) {

        $app->subscribe(new Editor);

        $app->on('app.request', function () use ($app) {
            $app['view']->addHelper(new EditorHelper());
            $app['scripts']->register('editor', 'app/system/modules/editor/app/editor.js', ['finder', 'uikit-htmleditor']);
            $app['scripts']->register('editor-image-replace', 'app/system/modules/editor/views/image.replace.php', '~editor', 'template');
            $app['scripts']->register('editor-link-replace', 'app/system/modules/editor/views/link.replace.php', '~editor', 'template');
            $app['scripts']->register('editor-video-replace', 'app/system/modules/editor/views/video.replace.php', '~editor', 'template');
        });

    },

    'autoload' => [

        'Pagekit\\Editor\\' => 'src'

    ]

];
