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
        App::view()->meta(['title' => __('Permissions')]);
        App::view()->style('permission-index', 'extensions/system/assets/css/user.css');
        App::view()->script('permission-index', 'extensions/system/modules/user/app/role.js', ['vue-system', 'uikit-sticky']);
        App::view()->addData('permission', [
                'data'   => [
                    'permissions' => App::permissions(),
                    'roles'       => Role::query()->orderBy('priority')->get()
                ]
            ]);
    }
}
