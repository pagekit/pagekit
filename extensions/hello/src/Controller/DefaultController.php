<?php

namespace Pagekit\Hello\Controller;

use Pagekit\Framework\Controller\Controller;

/**
 * @Route("/hello")
 */
class DefaultController extends Controller
{
    /**
     * @Route("/", name="@hello/world")
     * @Route("/{name}", name="@hello/name")
     * @View("hello/index.razr.php")
     */
    public function indexAction($name = 'World')
    {
        $names = explode(",", $name);
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

}
