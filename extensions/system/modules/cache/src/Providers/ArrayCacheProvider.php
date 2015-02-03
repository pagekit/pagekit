<?php

namespace Pagekit\Cache\Providers;

use Pagekit\Cache;
use Pagekit\Cache\CacheProviderInterface;

class ArrayCacheProvider implements CacheProviderInterface
{
	function getPriority()
	{
		return 100;
	}

	function getSlug()
	{
		return 'array';
	}

	function getName()
	{
		return __('Array cache');
	}

	function isSupported()
	{
		return true;
	}

	function createCache(array $config)
	{
		return new ArrayCache();
	}
}
