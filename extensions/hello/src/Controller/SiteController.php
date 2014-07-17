<?php

namespace Pagekit\Hello\Controller;

use Pagekit\Framework\Controller\Controller;

/**
 * @Route("/hello")
 */
class SiteController extends Controller
{
    /**
     * @Response("extension://hello/views/index.razr")
     */
    public function indexAction()
    {
        return ['head.title' => 'Hello World'];
    }

    /**
     * @Route("/greet", name="@hello/greet/world")
     * @Route("/greet/{name}", name="@hello/greet/name")
     * @Response("extension://hello/views/greet.razr")
     */
    public function greetAction($name = 'World')
    {
        $names = explode(',', $name);
        return ['head.title' => __('Hello %name%', ['%name%' => $names[0]]), 'names' => $names];
    }

    /**
     * @Route("/view/{id}", name="@hello/view/id", requirements={"id"="\d+"})
     * @Response("extension://hello/views/view.razr")
     */
    public function viewAction($id=1)
    {
        return ['head.title' => __('View article'), 'id' => $id];
    }

    public function anotherViewAction()
    {
        $view = 'extension://hello/views/view.razr';
        $data = ['head.title' => __('View article'), 'id' => 1];
        return $this['view']->render($view, $data);
    }

    public function redirectAction()
    {
        return $this['response']->redirect('@hello/greet/name', ['name' => 'Someone']);
    }

    public function jsonAction()
    {
        $data = ['error' => true, 'message' => 'There is nothing here. Move along.'];
        return $this['response']->json($data);
    }

    public function downloadAction()
    {
        return $this['response']->download('extensions/hello/extension.svg');
    }

    function forbiddenAction()
    {
        return $this['response']->create(__('Permission denied.'), 401);
    }
}
