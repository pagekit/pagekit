<?php

namespace Pagekit\User\Controller;

use Pagekit\Application as App;
use Pagekit\User\Entity\Role;

/**
 * @Route("/user/permission")
 * @Access("system: manage user permissions", admin=true)
 */
class PermissionController
{
    /**
     * @Response("extensions/system/modules/user/views/admin/permission.php")
     */
    public function indexAction()
    {
        App::scripts('permission', [
            'config' => [
                'urls' => [
                    'base' => App::url()->base(),
                    'role' => App::url('@api/system/role'),
                ]
            ],
            'data'   => [
                'permissions' => App::permissions(),
                'roles'       => Role::query()->orderBy('priority')->get()
            ]
        ], [], 'export');

        return ['head.title' => __('Permissions')];
    }
}
