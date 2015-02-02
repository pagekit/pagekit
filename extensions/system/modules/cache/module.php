<?php

use Doctrine\Common\Cache\ApcCache;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\XcacheCache;
use Pagekit\Cache\Cache;
use Pagekit\Cache\FilesystemCache;
use Pagekit\Cache\PhpFileCache;

return [

    'name' => 'system/cache',

    'main' => function ($app, $config) {

        $module = new Cache;

        foreach ($config['config'] as $name => $config) {
            $app[$name] = function() use ($config, $module) {

                $supports = $module->supports();

                if (!isset($config['storage'])) {
                    throw new \RuntimeException('Cache storage missing.');
                }

                if ($config['storage'] == 'auto' || !in_array($config['storage'], $supports)) {
                    $config['storage'] = end($supports);
                }

                switch ($config['storage']) {

                    case 'array':
                        $cache = new ArrayCache;
                        break;

                    case 'apc':
                        $cache = new ApcCache;
                        break;

                    case 'xcache':
                        $cache = new XcacheCache;
                        break;

                    case 'file':
                        $cache = new FilesystemCache($config['path']);
                        break;

                    case 'phpfile':
                        $cache = new PhpFileCache($config['path']);
                        break;

                    default:
                        throw new \RuntimeException('Unknown cache storage.');
                        break;
                }

                if ($prefix = isset($config['prefix']) ? $config['prefix'] : false) {
                    $cache->setNamespace($prefix);
                }

                return $cache;
            };
        }

        return $module;
    },

    'autoload' => [

        'Pagekit\\Cache\\' => 'src'

    ],

    'config' => [

    ]

];
