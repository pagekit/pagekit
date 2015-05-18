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
     */
    public function indexAction($id = null)
    {
        return [
            '$view' => [
                'title' => __('Roles'),
                'name'  => 'system/user:views/admin/role.php'
            ],
            '$config' => [
                'role' => $id
            ],
            '$data' => [
                'permissions' => App::module('system/user')->getPermissions(),
                'roles'       => Role::query()->orderBy('priority')->get()
            ]
        ];
    }
}
