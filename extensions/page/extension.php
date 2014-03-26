<?php

return array(

    'main' => 'Pagekit\\Page\\PageExtension',

    'autoload' => array(

        'Pagekit\\Page\\' => 'src'

    ),

    'resources' => array(

        'export' => array(
            'view'  => 'views',
            'asset' => 'assets'
        )

    ),

    'controllers' => 'src/Controller/*Controller.php',

    'events' => array(

        'system.link' => function($event) {
            $event->register('Pagekit\Page\PageLink');
        },

        'admin.menu' => function($event) {

            $event->addItems(array(

                'page' => array(
                    'label'  => __('Pages'),
                    'url'    => '@page/page/index',
                    'active' => '/admin/page*',
                    'access' => 'page: manage pages'
                )

            ));
        },

        'system.permission' => function($event) {

            $event->setPermissions('page', array(

                'page: manage pages' => array(
                    'title' => __('Manage pages')
                )

            ));
        }

    )

);
