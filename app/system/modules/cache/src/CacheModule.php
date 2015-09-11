<?php

namespace Pagekit\Cache;

use Doctrine\Common\Cache\ApcCache;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\XcacheCache;
use Pagekit\Application as App;
use Pagekit\Module\Module;

class CacheModule extends Module
{
    /**
     * {@inheritdoc}
     */
    public function main(App $app)
    {
        foreach ($this->config['caches'] as $name => $config)  {
            $app[$name] = function() use ($config) {

                $supports = $this->supports();

                if (!isset($config['storage'])) {
                    throw new \RuntimeException('Cache storage missing.');
                }

                if ($this->config['nocache']) {
                    $config['storage'] = 'array';
                } else if ($config['storage'] == 'auto' || !in_array($config['storage'], $supports)) {
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

    /**
     * Clear cache on terminate event.
     */
    public function clearCache(array $options = [])
    {
        App::on('terminate', function() use ($options) {
            $this->doClearCache($options);
        }, -512);
    }

    /**
     * TODO: clear opcache
     */
    public function doClearCache(array $options = [])
    {
        // clear cache
        if (empty($options) || @$options['cache']) {
            App::cache()->flushAll();

            foreach ((array) glob(App::get('path.cache') . '/*.cache') as $file) {
                @unlink($file);
            }
        }

        // clear temp folder
        if (@$options['temp']) {
            foreach (App::finder()->in(App::get('path.temp'))->depth(0)->ignoreDotFiles(true) as $file) {
                App::file()->delete($file->getPathname());
            }
        }
    }
}
