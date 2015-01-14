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
     * @Response("extensions/hello/views/admin/index.razr")
     */
    public function indexAction()
    {
        return ['head.title' => __('Hello')];
    }
}
