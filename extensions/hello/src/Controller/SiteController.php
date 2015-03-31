<?php

namespace Pagekit\Hello\Controller;

use Pagekit\Application as App;

/**
 * @Route("/hello")
 */
class SiteController
{
    /**
     * @Response("hello:views/index.razr")
     */
    public function indexAction()
    {
        return ['head.title' => 'Hello World'];
    }

    /**
     * @Route("/greet", name="@hello/greet/world")
     * @Route("/greet/{name}", name="@hello/greet/name")
     * @Response("hello:views/greet.razr")
     */
    public function greetAction($name = 'World')
    {
        $names = explode(',', $name);
        return ['head.title' => __('Hello %name%', ['%name%' => $names[0]]), 'names' => $names];
    }

    /**
     * @Route("/view/{id}", name="@hello/view/id", requirements={"id"="\d+"})
     * @Response("hello:views/view.razr")
     */
    public function viewAction($id = 1)
    {
        return ['head.title' => __('View article'), 'id' => $id];
    }

    public function anotherViewAction()
    {
        $data = ['head.title' => __('View article'), 'id' => 1];
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
