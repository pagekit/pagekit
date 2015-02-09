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
     * @Response("extensions/system/modules/site/views/index.php")
     */
    public function indexAction()
    {
        App::exports('site')->add([
            'config'    => [
                'url'          => App::url()->base(),
                'route'        => App::url('@system/site'),
                'url.template' => App::url('@system/template'),
                'csrf'         => App::csrf()->generate()
            ],
            'data'   => [
                'types' => App::module('system/site')->getTypes(),
                'roles' => Role::findAll()
            ],
            'templates' => [
                'site.edit' => App::view('extensions/system/modules/site/views/tmpl/site.edit.php'),
                'site.list' => App::view('extensions/system/modules/site/views/tmpl/site.list.php')
            ]
        ]);

        return ['head.title' => __('Nodes')];
    }
}
