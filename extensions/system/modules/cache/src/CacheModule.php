<?php

namespace Pagekit\Cache;

use Pagekit\Application as App;
use Pagekit\Module\Module;
use Doctrine\Common\Cache\ApcCache;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\XcacheCache;

class CacheModule extends Module
{
    /**
     * {@inheritdoc}
     */
    public function load(App $app, array $config)
    {
        $supports = $this->supports();

        foreach ($config['config'] as $name => $config) {
            $app[$name] = function() use ($config, $supports) {

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
    }

    /**
     * Returns list of supported caches or boolean for individual cache.
     *
     * @param  string $name
     * @return array|boolean
     */
    public static function supports($name = null)
    {
        $supports = ['phpfile', 'array', 'file'];

        if (extension_loaded('apc') && class_exists('\APCIterator')) {
            if (!extension_loaded('apcu') || version_compare(phpversion('apcu'), '4.0.2', '>=')) {
                $supports[] = 'apc';
            }
        }

        if (extension_loaded('xcache') && ini_get('xcache.var_size')) {
            $supports[] = 'xcache';
        }

        return $name? in_array($name, $supports) : $supports;
    }
}
