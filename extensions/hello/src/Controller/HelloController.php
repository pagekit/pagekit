<?php

namespace Pagekit\Hello\Controller;

/**
 * @Route("/hello")
 * @Access(admin=true)
 */
class HelloController
{
    /**
     * @Response("hello:views/admin/index.razr")
     */
    public function indexAction()
    {
        return [
           '$meta' => [
                'title' => __('Hello')
            ]
        ];
    }
}
