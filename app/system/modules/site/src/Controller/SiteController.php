<?php

namespace Pagekit\Site\Controller;

use Pagekit\Application as App;

/**
 * @Access("site: manage site")
 */
class SiteController
{
    /**
     * @Access(admin=true)
     */
    public function indexAction()
    {
        $site = App::module('system/site');

        return [
            '$view' => [
                'title' => __('Nodes'),
                'name'  => 'site:views/admin/index.php'
            ],
            '$data' => [
                'types' => array_values($site->getTypes())
            ]
        ];
    }

    /**
     * @Route("api/site", name="api/site")
     * @Access("site: manage site")
     */
    public function apiSiteAction()
    {
        return [
            'menus' => json_decode(App::forward('@site/api/menu')->getContent()),
            'nodes' => json_decode(App::forward('@site/api/node')->getContent())
        ];
    }
}
