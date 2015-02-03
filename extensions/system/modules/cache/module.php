<?php

use Pagekit\Cache\CacheProviderCollection;

return [

    'name' => 'system/cache',

    'main' => function ($app, $config) {
        $providerCollection = new CacheProviderCollection();
        $app['cache.providers'] = $providerCollection;
        foreach ($config['config'] as $name => $config) {
            $app[$name] = function() use ($config, $providerCollection) {

                if (!isset($config['storage'])) {
                    throw new \RuntimeException('Cache storage missing.');
                }
                $provider = $providerCollection->get($config['storage']);
                if (!$provider){
                    throw new \RuntimeException('Unknown cache storage.');
                }
                $cache = $provider->createCache($config);

                if ($prefix = isset($config['prefix']) ? $config['prefix'] : false) {
                    $cache->setNamespace($prefix);
                }

                return $cache;
            };
        }
    },

    'autoload' => [

        'Pagekit\\Cache\\' => 'src'

    ],

    'config' => [

    ]

];
