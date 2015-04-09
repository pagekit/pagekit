<?php

namespace Pagekit\Cache\Controller;

use Pagekit\Application as App;

/**
 * @Access(admin=true)
 */
class CacheController
{
    /**
     * @Request({"caches": "array"}, csrf=true)
     * @Response("json")
     */
    public function clearAction($caches)
    {
        App::module('system/cache')->clearCache($caches);

        return ['message' => __('Cache cleared!')];
    }
}
