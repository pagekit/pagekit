<?php

namespace Pagekit\Cache\Providers;

use Pagekit\Cache\CacheProviderInterface;
use Pagekit\Cache\PhpFileCache;

class PhpFileCacheProvider implements CacheProviderInterface
{
	function getPriority()
	{
		return 70;
	}

	function getSlug()
	{
		return 'phpfile';
	}

	function getName()
	{
		return __('PHP file cache');
	}

	function isSupported(){
		return true;
	}

	function createCache(array $config)
	{
		return new PhpFileCache($config['path']);
	}
}
