<?php

return [

    'name' => 'system/console',

    'autoload' => [

        'Pagekit\\Console\\' => 'src'

    ],

    'events' => [

        'console.init' => function ($event, $console) {

            $namespace = 'Pagekit\\Console\\';

            foreach (glob(__DIR__.'/src/*Command.php') as $file) {
                $class = $namespace.basename($file, '.php');
                $console->add(new $class);
            }

        }

    ]

];
