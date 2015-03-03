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
        App::scripts('site', [
            'config'    => [
                'url'      => App::url()->base(),
                'url.node' => App::url('@system/node'),
                'url.menu' => App::url('@system/menu'),
                'url.tmpl' => App::url('@system/template'),
                'csrf'     => App::csrf()->generate()
            ],
            'data'      => [
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
