<?php

namespace Pagekit\Cache\Providers;

use Doctrine\Common\Cache\XCache;
use Pagekit\Cache\CacheProviderInterface;

class XCacheProvider implements CacheProviderInterface
{
	function getPriority()
	{
		return 80;
	}

	function getSlug()
	{
		return 'xcache';
	}

	function getName()
	{
		return __('XCache');
	}

	function isSupported(){
		return extension_loaded('xcache') && ini_get('xcache.var_size');
	}

	function createCache(array $config)
	{
		return new XCacheCache;
	}
}
