<?php

namespace Pagekit\Site\Controller;

use Pagekit\Application as App;
use Pagekit\Kernel\Exception\NotFoundException;
use Pagekit\Site\Entity\Node;

/**
 * @Access("site: manage site", admin=true)
 * @Route(name="")
 */
class SiteController
{
    /**
     * @Response("site:views/admin/index.php")
     */
    public function indexAction()
    {
        App::view()->script('site', 'site:app/site.js', ['system', 'vue-validator', 'uikit-nestable', 'site-tree']);

        $site = App::module('system/site');

        return [
            '$meta' => [
                'title' => __('Nodes')
            ],
            '$data' => [
                'types'     => array_values($site->getTypes()),
                'frontpage' => $site->config('frontpage_node')
            ]
        ];
    }

    /**
     * @Request({"type", "id": "int"})
     * @Response("json")
     */
    public function editAction($type = '', $id = 0)
    {
        if (!$node = Node::find($id)) {

            if (!$type || $id) {
                throw new NotFoundException;
            }

            $node = new Node;
            $node->setType($type);
        }

        return [
            'node' => $node,
            'view' => App::view('site:views/admin/edit.php', ['sections' => App::module('system/site')->getSections($node->getType()), 'node' => $node]),
            'data' => App::view()->data()->get('site')
        ];
    }
}
