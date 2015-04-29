<?php

namespace Pagekit\Hello\Controller;

use Pagekit\Application as App;


/**
 * @Access("hello: settings", admin=true)
 * @Route(name="")
 */
class HelloController
{

    public function indexAction()
    {
        return "TODO";
    }

    public function settingsAction()
    {
        return [
            '$view' => [
                'title' => __('Hello Settings'),
                'name'  => 'hello:views/admin/settings.php'
            ],
            '$data' => [
                'config' => App::module('system/hello')->config()
            ]
        ];
    }
}
