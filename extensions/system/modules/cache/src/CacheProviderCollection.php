<?php

namespace Pagekit\Cache;

use Pagekit\Cache\Providers;
use Pagekit\Cache\Providers\ApcCacheProvider;
use Pagekit\Cache\Providers\ArrayCacheProvider;
use Pagekit\Cache\Providers\FilesystemCacheProvider;
use Pagekit\Cache\Providers\PhpFileCacheProvider;
use Pagekit\Cache\Providers\XCacheProvider;
use Pagekit\Cache\Providers\RedisCacheProvider;

class CacheProviderCollection
{
	protected $cacheProviders = array();

	function __construct()
	{
		$this->add(new ApcCacheProvider());
		$this->add(new ArrayCacheProvider());
		$this->add(new FilesystemCacheProvider());
		$this->add(new PhpFileCacheProvider());
		$this->add(new XCacheProvider());
		$this->add(new RedisCacheProvider());
	}

	function add(CacheProviderInterface $provider)
	{
		$priority = $provider->getPriority();
		while (isset($this->cacheProviders[$priority]))
			$priority++;
		$this->cacheProviders[$priority] = $provider;
		ksort($this->cacheProviders);

	}

	function getDefault()
	{
		if (!count($this->cacheProviders)){
			return null;
		}
		foreach($this->cacheProviders as $provider){
			if ($provider->isSupported()){
				return $provider;
			}
		}
		return null;
	}

	function get($slug)
	{
		if ($slug == 'auto'){
			return $this->getDefault();
		}
		foreach($this->cacheProviders as $provider){
			if ($provider->getSlug() == $slug){
				if ($provider->isSupported()){
					return $provider;
				} else {
					return $this->getDefault();
				}
			}
		}
		return null;
	}

	function getList()
	{
		return array_values($this->cacheProviders);
	}

	function getSupportedList(){
		$result = [];
		foreach($this->cacheProviders as $provider){
			if ($provider->isSupported()){
				$result []= $provider;
			}
		}
		return $result;
	}

}
