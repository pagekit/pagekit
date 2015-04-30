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
        $config = App::module('hello')->config();

        return [
            '$view' => [
                'title' => __('Hello Settings'),
                'name'  => 'hello:views/admin/settings.php'
            ],
            '$data' => [
                'config' => $config
            ],

            'config' => $config
        ];
    }
}
