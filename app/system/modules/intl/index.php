<?php

return [

    'name' => 'system/intl',

    'main' => 'Pagekit\\Intl\\IntlModule',

    'autoload' => [

        'Pagekit\\Intl\\' => 'src'

    ],

    'resources' => [

        'system/intl:' => ''

    ],

    'routes' => [
        '/system/intl' => [
            'name' => '@system/intl',
            'controller' => 'Pagekit\\Intl\\Controller\\IntlController'
        ],
    ],

    'config' => [

        'locale' => [

            'admin' => 'de_DE',
            'site' => 'en_US'

        ]

    ],

    'events' => [

        'view.system:modules/settings/views/settings' => function ($event, $view) use ($app) {

            $languages = $this->getLanguages();

            $locales = [];
            foreach ($this->getAvailableLanguages() as $id) {
                $tag = str_replace('_', '-', $id);

                if (isset($languages[$tag])) {
                    $locales[$id] = $languages[$tag];
                } elseif (isset($languages[substr($tag, 0, 2)])) {
                    $locales[$id] = $languages[substr($tag, 0, 2)];
                }
            }

            asort($locales);

            $view->data('$intl', ['locales' => $locales]);
            $view->data('$settings', ['options' => [$this->name => $this->config]]);
            $view->script('settings-intl', 'app/system/modules/intl/app/bundle/settings.js', 'settings');
        }

    ]

];
