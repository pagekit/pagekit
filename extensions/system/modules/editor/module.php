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
            $app['view']->tmpl()->register('image.modal', 'extensions/system/views/tmpl/image.modal.razr');
            $app['view']->tmpl()->register('image.replace', 'extensions/system/views/tmpl/image.replace.razr');
            $app['view']->tmpl()->register('link.modal', 'extensions/system/views/tmpl/link.modal.razr');
            $app['view']->tmpl()->register('link.replace', 'extensions/system/views/tmpl/link.replace.razr');
            $app['view']->tmpl()->register('video.modal', 'extensions/system/views/tmpl/video.modal.razr');
            $app['view']->tmpl()->register('video.replace', 'extensions/system/views/tmpl/video.replace.razr');

            $app['scripts']->register('editor', 'extensions/system/modules/editor/assets/js/editor.js', 'uikit-htmleditor');
            $app['scripts']->register('editor-link', 'extensions/system/modules/editor/assets/js/link.js', 'editor');
            $app['scripts']->register('editor-video', 'extensions/system/modules/editor/assets/js/video.js', 'editor');
            $app['scripts']->register('editor-image', 'extensions/system/modules/editor/assets/js/image.js', 'editor');
            $app['scripts']->register('editor-urlresolver', 'extensions/system/modules/editor/assets/js/urlresolver.js', 'editor');

            $app['tmpl.razr']->addDirective(new FunctionDirective('editor', [$helper, 'render']));
        });

    },

    'autoload' => [

        'Pagekit\\Editor\\' => 'src'

    ]

];
