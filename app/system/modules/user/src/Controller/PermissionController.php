<?php

namespace Pagekit\User\Controller;

use Pagekit\Application as App;
use Pagekit\User\Entity\Role;

/**
 * @Access("user: manage user permissions", admin=true)
 */
class PermissionController
{
    /**
     * @Response("user:views/admin/permission.php")
     */
    public function indexAction()
    {
        return [
            '$meta' => [
                'title' => __('Permissions')
            ],
            '$data' => [
                'permissions' => App::permissions(),
                'roles'       => Role::query()->orderBy('priority')->get()
            ]
        ];
    }
}
