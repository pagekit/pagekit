<?php

namespace Pagekit\Finder\Controller;

use Pagekit\Application as App;

/**
 * @Access("system: manage storage", admin=true)
 */
class StorageController
{
    /**
     * @Response("system:modules/finder/views/storage.php")
     */
    public function indexAction()
    {
        return [
            '$meta' => [
                'title' => __('Storage')
            ],
            'root' => App::system()->config('storage'),
            'mode' => 'write'
        ];
    }
}
