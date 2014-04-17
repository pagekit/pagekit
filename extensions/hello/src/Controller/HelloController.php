<?php

namespace Pagekit\Hello\Controller;

use Pagekit\Framework\Controller\Controller;

/**
 * @Route("/hello")
 * @Access(admin=true)
 */
class HelloController extends Controller
{
    /**
     * @View("hello/admin/index.razr.php")
     */
    public function indexAction()
    {
        return array('head.title' => __('Hello'));
    }
}
