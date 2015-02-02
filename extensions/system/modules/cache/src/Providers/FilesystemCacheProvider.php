<?php

namespace Pagekit\Cache\Providers;
use Pagekit\Cache\CacheProviderInterface;
use Pagekit\Cache\FilesystemCache;

class FilesystemCacheProvider implements CacheProviderInterface
{
	function getPriority()
	{
		return 70;
	}

	function getSlug()
	{
		return 'file';
	}

	function getName()
	{
		return __('File cache');
	}

	function isSupported(){
		return true;
	}

	function createCache(array $config)
	{
		return new FilesystemCache($config['path']);
	}
}
