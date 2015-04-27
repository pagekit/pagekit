<?php

namespace Pagekit\Hello\Controller;

use Pagekit\Application as App;

/**
 * @Route("/hello")
 */
class SiteController
{
    public function indexAction()
    {
        return [
            '$view' => [
                'title' => __('Hello World'),
                'name'  => 'hello:views/index.razr'
            ]
        ];
    }

    /**
     * @Route("/greet", name="@hello/greet/world")
     * @Route("/greet/{name}", name="@hello/greet/name")
     */
    public function greetAction($name = 'World')
    {
        $names = explode(',', $name);

        return [
            '$view' => [
                'title' => __('Hello %name%', ['%name%' => $names[0]]),
                'name'  => 'hello:views/greet.razr'
            ],
            'names' => $names
        ];
    }

    /**
     * @Route("/view/{id}", name="@hello/view/id", requirements={"id"="\d+"})
     */
    public function viewAction($id = 1)
    {
        return [
            '$view' => [
                'title' => __('View article'),
                'name'  => 'hello:views/view.razr'
            ],
            'id' => $id
        ];
    }

    public function anotherViewAction()
    {
        $data = [
           '$view' => [
                'title' => __('View article')
            ],
            'id' => 1
        ];

        return App::view('hello:views/view.razr', $data);
    }

    public function redirectAction()
    {
        return App::response()->redirect('@hello/greet/name', ['name' => 'Someone']);
    }

    public function jsonAction()
    {
        $data = ['error' => true, 'message' => 'There is nothing here. Move along.'];

        return App::response()->json($data);
    }

    public function downloadAction()
    {
        return App::response()->download('extensions/hello/extension.svg');
    }

    function forbiddenAction()
    {
        return App::response(__('Permission denied.'), 401);
    }
}
