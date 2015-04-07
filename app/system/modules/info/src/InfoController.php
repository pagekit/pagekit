<?php

namespace Pagekit\System\Info;

use Pagekit\Application as App;
use Pagekit\Application\Controller;

/**
 * @Access(admin=true)
 */
class InfoController extends Controller
{
    /**
     * @Response("system:modules/info/views/info.php")
     */
    public function indexAction()
    {
        return [
            '$meta' => ['title' => __('System Information')],
            '$info' => App::systemInfo()->get()
        ];
    }
}
