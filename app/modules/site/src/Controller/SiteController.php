<?php

namespace Pagekit\Site\Controller;

use Pagekit\Application as App;
use Pagekit\Application\Controller;
use Pagekit\User\Entity\Role;

/**
 * @Access("system: manage site", admin=true)
 */
class SiteController extends Controller
{
    /**
     * @Response("app/modules/site/views/index.php")
     */
    public function indexAction()
    {
        return [
            '$meta' => [
                'title' => __('Nodes')
            ],
            '$data'      => [
                'types' => array_values(App::module('system/site')->getTypes()),
                'roles' => Role::findAll()
            ]
        ];
    }
}
