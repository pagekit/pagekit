<?php

namespace Pagekit\Site\Controller;

use Pagekit\Application as App;

/**
 * @Access("site: manage site")
 */
class SiteController
{
    /**
     * @Route("site")
     * @Access(admin=true)
     */
    public function indexAction()
    {
        $site = App::module('system/site');

        return [
            '$view' => [
                'title' => __('Pages'),
                'name'  => 'site:views/admin/index.php'
            ],
            '$data' => [
                'types' => array_values($site->getTypes())
            ]
        ];
    }

    /**
     * @Route("site/settings")
     * @Access(admin=true)
     */
    public function settingsAction()
    {
        return [
            '$view' => [
                'title' => __('Settings'),
                'name'  => 'site:views/admin/settings.php'
            ],
            '$data' => [
                'config' => App::system()->config(['site.', 'maintenance.'])
            ]
        ];
    }

    /**
     * @Route("api/site", name="api/site")
     */
    public function apiSiteAction()
    {
        return [
            'menus' => json_decode(App::forward('@site/api/menu')->getContent()),
            'nodes' => json_decode(App::forward('@site/api/node')->getContent())
        ];
    }
}
