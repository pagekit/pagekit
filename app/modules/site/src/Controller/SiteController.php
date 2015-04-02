<?php

namespace Pagekit\Site\Controller;

use Pagekit\Application as App;
use Pagekit\Application\Controller;
use Pagekit\Site\Entity\Node;
use Pagekit\Site\Event\EditEvent;
use Pagekit\User\Entity\Role;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Access("site: manage site", admin=true)
 * @Route(name="")
 */
class SiteController extends Controller
{
    /**
     * @Response("site:views/admin/index.php")
     */
    public function indexAction()
    {
        App::view()->script('site', 'app/modules/site/app/site.js', ['vue-system', 'vue-validator', 'uikit-nestable']);

        return [
            '$meta' => [
                'title' => __('Nodes')
            ],
            '$data'      => [
                'types' => array_values(App::module('site')->getTypes()),
                'roles' => Role::findAll()
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
                throw new NotFoundHttpException;
            }

            $node = new Node;
            $node->setType($type);
        }

        return [
            'node' => $node,
            'view' => App::view('site:views/admin/edit.php', ['sections' => App::module('site')->getSections($node->getType()), 'node' => $node])
        ];
    }
}
