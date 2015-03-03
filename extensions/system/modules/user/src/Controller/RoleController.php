<?php

namespace Pagekit\User\Controller;

use Pagekit\Application as App;
use Pagekit\Application\Controller;
use Pagekit\User\Entity\Role;

/**
 * @Route("/user/role")
 * @Access("system: manage user permissions", admin=true)
 */
class RoleController extends Controller
{
    /**
     * @Request({"id": "int"})
     * @Response("extensions/system/modules/user/views/admin/role.php")
     */
    public function indexAction($id = null)
    {
        App::scripts('role', [
            'config' => [
                'role' => $id
            ],
            'data'   => [
                'permissions' => App::permissions(),
                'roles'       => Role::query()->orderBy('priority')->get()
            ]
        ]);

        return ['head.title' => __('Roles')];
    }
}
