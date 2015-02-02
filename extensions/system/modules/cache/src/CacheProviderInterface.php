<?php

namespace Pagekit\Cache;

interface CacheProviderInterface
{
	function getSlug();
	function getName();
	function getPriority();
	function isSupported();
	function createCache(array $config);
}
