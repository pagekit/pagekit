<?php

namespace Pagekit\Cache;

use Pagekit\Application as App;

class CacheModule
{
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
