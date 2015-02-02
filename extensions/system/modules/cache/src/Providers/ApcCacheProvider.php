<?php

namespace Pagekit\Cache\Providers;

use Doctrine\Common\Cache\ApcCache;
use Pagekit\Cache\CacheProviderInterface;

class ApcCacheProvider implements CacheProviderInterface
{
	function getPriority()
	{
		return 90;
	}

	function getSlug()
	{
		return 'apc';
	}

	function getName()
	{
		return __('APC Cache');
	}



	function createCache(array $config)
	{
		return new ApcCache();
	}

	function isSupported(){
		return extension_loaded('apc') && class_exists('\APCIterator') &&
			(!extension_loaded('apcu') || version_compare(phpversion('apcu'), '4.0.2', '>='));
	}
}
