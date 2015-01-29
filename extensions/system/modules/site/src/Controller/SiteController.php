<?php

namespace Pagekit\Site\Controller;

use Pagekit\Application as App;
use Pagekit\Application\Controller;
use Pagekit\Site\Event\ConfigEvent;
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
        $config = App::trigger('site.config', new ConfigEvent([
            'config'    => [
                'url'          => App::url()->base(),
                'route'        => App::url('@system/node'),
                'url.template' => App::url('@system/template'),
                'csrf'         => App::csrf()->generate()
            ],
            'data'   => [
                'types' => App::get('site.types')->getTypes(),
                'roles' => Role::findAll(),
                'menus'  => ['Main', 'Sidebar']
            ],
            'templates' => [
                'site.edit' => App::view('extensions/system/modules/site/views/tmpl/site.edit.php'),
                'site.list' => App::view('extensions/system/modules/site/views/tmpl/site.list.php')
            ]
        ]))->getConfig();

        App::on('kernel.view', function () use ($config) {
            App::scripts('site-config', sprintf('var %s = %s;', 'site', json_encode($config)), [], 'string');
        });

        return ['head.title' => __('Nodes')];
    }
}
