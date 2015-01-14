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
     * @Response("extensions/system/views/admin/user/permission.razr")
     */
    public function indexAction()
    {
        $roles = Role::query()->orderBy('priority')->get();

        return ['head.title' => __('Permissions'), 'roles' => $roles, 'permissions' => App::permissions()];
    }

    /**
     * @Request({"permissions": "array"}, csrf=true)
     * @Response("json")
     */
    public function saveAction($permissions = [])
    {
        foreach (Role::findAll() as $role) {
            $role->setPermissions(isset($permissions[$role->getId()]) ? $permissions[$role->getId()] : []);
            Role::save($role);
        }

        return App::request()->isXmlHttpRequest() ? ['message' => __('Permissions saved!')] : $this->redirect('@system/permission');
    }
}
