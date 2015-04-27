<?php

namespace Pagekit\Finder\Controller;

use Pagekit\Application as App;

/**
 * @Access("system: manage storage", admin=true)
 */
class StorageController
{
    public function indexAction()
    {
        return [
            '$view' => [
                'title' => __('Storage'),
                'name'  => 'system:modules/finder/views/storage.php'
            ],
            'root' => App::system()->config('storage'),
            'mode' => 'write'
        ];
    }
}
