<?php

namespace Pagekit\Site\Controller;

use Pagekit\Application as App;
use Pagekit\Kernel\Exception\NotFoundException;
use Pagekit\Site\Entity\Node;

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
                'name'  => 'site:views/index.php'
            ],
            '$data' => [
                'types' => array_values($site->getTypes()),
                'frontpage' => $site->config('frontpage')
            ]
        ];
    }

    /**
     * @Route("site/edit")
     * @Access(admin=true)
     * @Request({"id", "menu"})
     */
    public function editAction($id = '', $menu = '')
    {
        $site = App::module('system/site');

        if (is_numeric($id)) {
            $node = Node::find($id);
        } else {
            $node = new Node();
            $node->setType($id);

            if ($menu && !$site->getMenu($menu)) {
                throw new NotFoundException(__('Menu not found.'));
            }

            $node->setMenu($menu);
        }

        if (!$node) {
            throw new NotFoundException(__('Node not found.'));
        }

        if (!$type = $site->getType($node->getType())) {
            throw new NotFoundException(__('Type not found.'));
        }

        return [
            '$view' => [
                'title' => __('Pages'),
                'name'  => 'site:views/edit.php'
            ],
            '$data' => [
                'node' => $node,
                'type' => $type
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
                'name'  => 'site:views/settings.php'
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
