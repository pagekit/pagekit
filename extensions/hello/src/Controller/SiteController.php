<?php

namespace Pagekit\Hello\Controller;

use Pagekit\Application as App;

class SiteController
{
    /**
     * @Route("/")
     * @Route("/{name}", name="name")
     */
    public function indexAction($name = '')
    {
        $names = explode(',', $name ?: App::module('hello')->config('default'));

        return [
            '$view' => [
                'title' => __('Hello %name%', ['%name%' => $names[0]]),
                'name' => 'hello:views/index.php'
            ],
            'names' => $names
        ];
    }

    /**
     * @Route("/greet")
     * @Route("/greet/{name}", name="name")
     */
    public function greetAction($name = '')
    {
        $names = explode(',', $name ?: App::module('hello')->config('default'));

        return [
            '$view' => [
                'title' => __('Hello %name%', ['%name%' => $names[0]]),
                'name' => 'hello:views/index.php'
            ],
            'names' => $names
        ];
    }

    public function redirectAction()
    {
        return App::response()->redirect('@hello/greet', ['name' => 'Someone']);
    }

    public function jsonAction()
    {
        return ['message' => 'There is nothing here. Move along.'];
    }

    public function downloadAction()
    {
        return App::response()->download('extensions/hello/extension.svg');
    }

    function forbiddenAction()
    {
        App::abort(401, __('Permission denied.'));
    }
}
