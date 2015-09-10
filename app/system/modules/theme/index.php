<?php

return [

    'name' => 'system/theme',

    'type' => 'theme',

    'layout' => 'views:system/template.php',

    'events' => [

        'view.meta' => [function($event, $meta) use ($app) {
            $meta([
                'link:favicon' => [
                    'href' => $app['url']->getStatic('system/theme:favicon.ico'),
                    'rel' => 'shortcut icon',
                    'type' => 'image/x-icon'
                ],
                'link:appicon' => [
                    'href' => $app['url']->getStatic('system/theme:apple_touch_icon.png'),
                    'rel' => 'apple-touch-icon-precomposed'
                ]
            ]);
        }, 10],

        'view.layout' => function ($event, $view) use ($app) {

            if (!$app->isAdmin()) {
                return;
            }

            $user = $app['user'];

            $view->data('$pagekit', [
                'editor' => $app->module('system/editor')->config('editor'),
                'storage' => $app->module('system/finder')->config('storage'),
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'username' => $user->username
                ],
                'menu' => array_values($app['system']->getMenu()->getItems())
            ]);

            $subsets = 'latin,latin-ext';
            $subset  = __('_subset');

            if ('cyrillic' == $subset) {
    			$subsets .= ',cyrillic,cyrillic-ext';
    		} elseif ('greek' == $subset) {
    			$subsets .= ',greek,greek-ext';
    		} elseif ('vietnamese' == $subset) {
    			$subsets .= ',vietnamese';
    		}

            $event['subset'] = $subsets;

        }

    ],

    'resources' => [

        'system/theme:' => '',
        'views:system' => 'views'

    ]

];
