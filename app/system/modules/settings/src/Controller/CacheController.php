<?php

namespace Pagekit\System\Controller;

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
        App::module('cache')->clearCache($caches);

        return ['message' => __('Cache cleared!')];
    }
}
