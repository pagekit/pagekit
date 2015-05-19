<?php

namespace Pagekit\Site\Controller;

use Pagekit\Application as App;

/**
 * @Access("site: manage site", admin=true)
 */
class SiteController
{
    public function indexAction()
    {
        $site = App::module('system/site');

        return [
            '$view' => [
                'title' => __('Nodes'),
                'name'  => 'site:views/admin/index.php'
            ],
            '$data' => [
                'types'     => array_values($site->getTypes()),
                'frontpage' => $site->config('frontpage_node')
            ]
        ];
    }
}
