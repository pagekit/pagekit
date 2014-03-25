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
        return array('head.title' => __('Hello %name%', array('%name%' => $name)), 'name' => $name);
    }
}
