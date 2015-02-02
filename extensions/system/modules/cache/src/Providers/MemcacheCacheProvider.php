<?php

namespace Pagekit\Cache\Providers;

use Doctrine\Common\Cache\MemcacheCache;
use Pagekit\Cache\CacheProviderInterface;

class ApcCacheProvider implements CacheProviderInterface
{
	function getPriority()
	{
		return 90;
	}

	function getSlug()
	{
		return 'memcache';
	}

	function getName()
	{
		return __('Memcache');
	}

	function createCache(array $config)
	{   $config = array_merge([
			'host' => '127.0.0.1',
			'port' => '11211',
			'timeout' => 1,
			'persistent' => false
		], $config);
		$memcache = new Memcache();
		$memcache->addServer($config['host'], $config['port'], $config['persistent'], 10, $config['timeout']);
		$cache = new MemcacheCache();
		$cache->setMemcache($memcache);
		return $cache;
	}

	function isSupported(){
		return extension_loaded('memcache') && class_exists('\Memcache');
	}
}
