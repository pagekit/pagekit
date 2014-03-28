<?php

namespace Pagekit\Hello\Controller;

use Pagekit\Framework\Controller\Controller;

/**
 * @Route("/hello")
 */
class DefaultController extends Controller
{
    /**
     * @View("hello/index.razr.php")
     */
    public function indexAction()
    {
        return array('head.title' => 'Hello World');
    }

    /**
     * @Route("/greet", name="@hello/greet/world")
     * @Route("/greet/{name}", name="@hello/greet/name")
     * @View("hello/greet.razr.php")
     */
    public function greetAction($name = 'World')
    {
        $names = explode(',', $name);
        return array('head.title' => __('Hello %name%', array('%name%' => $names[0])), 'names' => $names);
    }

    /**
     * @Route("/view/{id}", name="@hello/view/id", requirements={"id"="\d+"})
     * @View("hello/view.razr.php")
     */
    public function viewAction($id=1)
    {
        return array('head.title' => __('View article'), 'id' => $id);
    }

    public function anotherViewAction()
    {
        $view = 'hello/view.razr.php';
        $data = array('head.title' => __('View article'), 'id' => 1);
        return $this('view')->render($view, $data);
    }

    public function redirectAction()
    {
        return $this('response')->redirect('@hello/greet/name', array('name' => 'Someone'));
    }

    public function jsonAction()
    {
        $data = array('error' => true, 'message' => 'There is nothing here. Move along.');
        return $this('response')->json($data);
    }

    public function downloadAction()
    {
        return $this('response')->download('extensions/hello/extension.svg');
    }

    function forbiddenAction()
    {
        return $this('response')->create(__('Permission denied.'), 401);
    }
}
