<?php

namespace Pagekit\Cache\Controller;

use Pagekit\Application as App;

class CacheController
{
    /**
     * @Access(admin=true)
     * @Request({"caches": "array"}, csrf=true)
     * @Response("json")
     */
    public function clearAction($caches)
    {
        App::module('system/cache')->clearCache($caches);

        return ['message' => __('Cache cleared!')];
    }
}
