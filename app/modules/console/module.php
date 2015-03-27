<?php

return [

    'name' => 'system/console',

    'main' => function ($app) {

        $app->on('console.init', function ($event) {

            $console = $event->getConsole();
            $namespace = 'Pagekit\\Console\\';

            foreach (glob(__DIR__.'/src/*Command.php') as $file) {
                $class = $namespace.basename($file, '.php');
                $console->add(new $class);
            }

        });

    },

    'autoload' => [

        'Pagekit\\Console\\' => 'src'

    ]

];
