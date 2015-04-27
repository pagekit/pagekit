<?php

namespace Pagekit\User\Controller;

use Pagekit\Application as App;
use Pagekit\User\Entity\Role;

/**
 * @Access("user: manage user permissions", admin=true)
 */
class PermissionController
{
    public function indexAction()
    {
        return [
            '$view' => [
                'title' => __('Permissions'),
                'name'  => 'system/user:views/admin/permission.php'
            ],
            '$data' => [
                'permissions' => App::permissions(),
                'roles'       => Role::query()->orderBy('priority')->get()
            ]
        ];
    }
}
