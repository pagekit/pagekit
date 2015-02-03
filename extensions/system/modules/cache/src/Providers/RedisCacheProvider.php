<?php

namespace Pagekit\Cache\Providers;

use Doctrine\Common\Cache\RedisCache;
use Pagekit\Cache\CacheProviderInterface;

class RedisCacheProvider implements CacheProviderInterface
{
	function getPriority()
	{
		return 90;
	}

	function getSlug()
	{
		return 'redis';
	}

	function getName()
	{
		return __('Redis');
	}

	function createCache(array $config)
	{   $config = array_merge([
		'host' => '127.0.0.1',
		'port' => '6379',
		'timeout' => 1,
		'persistent' => false
	], $config);
		$redis = new Redis();
		if ($config['persistent']){
			$redis->pconnect($config['host'], $config['port'], $config['timeout']);
		} else {
			$redis->connect($config['host'], $config['port'], $config['timeout']);
		}
		$cache = new RedisCache();
		$cache->setRedis($redis);
		return $cache;
	}

	function isSupported(){
		return class_exists('\Redis');
	}
}
