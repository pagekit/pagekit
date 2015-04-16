<?php

namespace Pagekit\User\Controller;

use Pagekit\Application as App;
use Pagekit\User\Entity\Role;

/**
 * @Access("user: manage user permissions", admin=true)
 */
class RoleController
{
    /**
     * @Request({"id": "int"})
     * @Response("system/user:views/admin/role.php")
     */
    public function indexAction($id = null)
    {
        return [
            '$meta' => [
                'title' => __('Roles')
            ],
            '$config' => [
                'role' => $id
            ],
            '$data' => [
                'permissions' => App::permissions(),
                'roles'       => Role::query()->orderBy('priority')->get()
            ]
        ];
    }
}
