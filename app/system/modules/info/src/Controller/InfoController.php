<?php

namespace Pagekit\Info\Controller;

use Pagekit\Application as App;

/**
 * @Access(admin=true)
 */
class InfoController
{
    /**
     * @Response("system:modules/info/views/info.php")
     */
    public function indexAction()
    {
        return [
            '$meta' => ['title' => __('Info')],
            '$info' => App::info()->get()
        ];
    }
}
