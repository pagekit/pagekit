<?php

namespace Pagekit\Site\Controller;

use Pagekit\Application as App;
use Pagekit\Site\Model\Node;
use Pagekit\User\Model\Role;

class NodeController
{
    /**
     * @Route("site/page", name="page")
     * @Access("site: manage site", admin=true)
     */
    public function indexAction()
    {
        $site = App::module('system/site');

        Node::fixOrphanedNodes();

        return [
            '$view' => [
                'title' => __('Pages'),
                'name'  => 'system/site/admin/index.php'
            ],
            '$data' => [
                'config' => [
                    'menus' => App::menu()->getPositions()
                ],
                'types' => array_values($site->getTypes())
            ]
        ];
    }

    /**
     * @Route("site/page/edit", name="page/edit")
     * @Access("site: manage site", admin=true)
     * @Request({"id", "menu"})
     */
    public function editAction($id = '', $menu = '')
    {
        $site = App::module('system/site');

        if (is_numeric($id)) {
            $node = Node::find($id);
        } else {
            $node = Node::create(['type' => $id]);

            if ($menu && !App::menu($menu)) {
                App::abort(404, 'Menu not found.');
            }

            $node->menu = $menu;
        }

        if (!$node) {
            App::abort(404, 'Node not found.');
        }

        if (!$type = $site->getType($node->type)) {
            App::abort(404, 'Type not found.');
        }

        return [
            '$view' => [
                'title' => __('Pages'),
                'name'  => 'system/site/admin/edit.php'
            ],
            '$data' => [
                'node' => $node,
                'type' => $type,
                'roles' => array_values(Role::findAll())
            ]
        ];
    }

    /**
     * @Route("site/settings")
     * @Access("site: manage settings", admin=true)
     */
    public function settingsAction()
    {
        return [
            '$view' => [
                'title' => __('Settings'),
                'name'  => 'system/site/admin/settings.php'
            ],
            '$data' => [
                'config' => App::module('system/site')->config(['title', 'description', 'maintenance.', 'logo', 'icons.', 'code.'])
            ]
        ];
    }

    /**
     * @Route("api/site/link", name="api/link")
     * @Request({"link"})
     * @Access("site: manage site")
     */
    public function linkAction($link)
    {
        return ['message' => 'success', 'url' => App::url($link, [], 'base') ?: $link];
    }
}
