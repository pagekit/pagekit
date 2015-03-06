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
        App::view()->metas(['title' => __('Roles')]);
        App::view()->styles('role-index', 'extensions/system/assets/css/user.css');
        App::view()->scripts('role-index', 'extensions/system/modules/user/app/role.js', 'vue-system');
        App::view()->addData('role', [
                'config' => [
                    'role' => $id
                ],
                'data'   => [
                    'permissions' => App::permissions(),
                    'roles'       => Role::query()->orderBy('priority')->get()
                ]
            ]);
    }
}
