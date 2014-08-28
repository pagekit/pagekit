<?php

return [

    'up' => function() use ($app) {

        foreach ($app['option'] as $name => $value) {

            $replace = preg_replace(['/:config$/', '/:config.widgets$/'], [':settings', ':settings.widgets'], $name);

            if ($replace != $name) {
                $app['option']->set($replace, $value, true);
                $app['option']->remove($name);
            }
        }
    }

];
