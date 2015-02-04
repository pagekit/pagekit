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

        foreach ($config['config'] as $name => $conf)  {
            $app[$name] = function() use ($config, $module, $conf) {

                $supports = $module->supports();

                if (!isset($conf['storage'])) {
                    throw new \RuntimeException('Cache storage missing.');
                }

                if ($config['nocache']) {
                    $conf['storage'] = 'array';
                } else if ($conf['storage'] == 'auto' || !in_array($conf['storage'], $supports)) {
                    $conf['storage'] = end($supports);
                }

                switch ($conf['storage']) {

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
                        $cache = new FilesystemCache($conf['path']);
                        break;

                    case 'phpfile':
                        $cache = new PhpFileCache($conf['path']);
                        break;

                    default:
                        throw new \RuntimeException('Unknown cache storage.');
                        break;
                }

                if ($prefix = isset($conf['prefix']) ? $conf['prefix'] : false) {
                    $cache->setNamespace($prefix);
                }

                return $cache;
            };
        }

        $app->on('system.settings.edit', function ($event) use ($app, $config, $module) {

            $supported = $module->supports();

            $caches = [
                'auto'   => ['name' => '', 'supported' => true],
                'apc'    => ['name' => 'APC', 'supported' => in_array('apc', $supported)],
                'xcache' => ['name' => 'XCache', 'supported' => in_array('xcache', $supported)],
                'file'   => ['name' => 'File', 'supported' => in_array('file', $supported)]
            ];

            $caches['auto']['name'] = 'Auto ('.$caches[end($supported)]['name'].')';

            $event->add('system/cache', __('Cache'), $app['view']->render('extensions/system/modules/cache/views/admin/settings.razr', ['config' => $config, 'cache' => $config['cache']['storage'] ?: 'auto', 'caches' => $caches]));
        });

        return $module;
    },

    'autoload' => [

        'Pagekit\\Cache\\' => 'src'

    ],

    'priority' => 12,

    'config' => [

    ],

    'nocache' => false

];
