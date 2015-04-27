<?php

namespace Pagekit\Hello\Controller;

/**
 * @Route("/hello")
 * @Access(admin=true)
 */
class HelloController
{
    public function indexAction()
    {
        return [
           '$view' => [
                'title' => __('Hello'),
                'name'  => 'hello:views/admin/index.razr'
            ]
        ];
    }
}
