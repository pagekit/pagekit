<?php

use Pagekit\Editor\Editor;
use Pagekit\Editor\EditorHelper;
use Razr\Directive\FunctionDirective;

return [

    'name' => 'system/editor',

    'main' => function ($app) {

        $app->subscribe(new Editor);

        $app->on('system.loaded', function ($event) use ($app) {

            $helper = new EditorHelper();

            $app['view']->addHelper('editor', $helper);
            $app['view']->tmpl()->register('image.modal', 'extensions/system/modules/editor/views/image.modal.php');
            $app['view']->tmpl()->register('image.replace', 'extensions/system/modules/editor/views/image.replace.php');
            $app['view']->tmpl()->register('link.modal', 'extensions/system/modules/editor/views/link.modal.php');
            $app['view']->tmpl()->register('link.replace', 'extensions/system/modules/editor/views/link.replace.php');
            $app['view']->tmpl()->register('video.modal', 'extensions/system/modules/editor/views/video.modal.php');
            $app['view']->tmpl()->register('video.replace', 'extensions/system/modules/editor/views/video.replace.php');

            $app['scripts']->register('editor', 'extensions/system/modules/editor/app/editor.js', ['uikit-htmleditor', 'finder']);

            $app['tmpl.razr']->addDirective(new FunctionDirective('editor', [$helper, 'render']));
        });

    },

    'autoload' => [

        'Pagekit\\Editor\\' => 'src'

    ]

];
