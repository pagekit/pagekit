<?php

namespace Pagekit\User\Controller;

use Pagekit\Application as App;
use Pagekit\Framework\Controller\Controller;
use Pagekit\User\Entity\Role;

/**
 * @Route("/user/role")
 * @Access("system: manage user permissions", admin=true)
 */
class RoleController extends Controller
{
    /**
     * @Request({"id": "int"})
     * @Response("extensions/system/views/admin/user/role.razr")
     */
    public function indexAction($id = null)
    {
        $roles = Role::query()->orderBy('priority')->get();

        if ($id === null && count($roles)) {
            $role = current($roles);
        } elseif ($id && isset($roles[$id])) {
            $role = $roles[$id];
        } else {
            $role = new Role;
            $role->setId(0);
        }

        $authrole = Role::find(Role::ROLE_AUTHENTICATED);

        return ['head.title' => __('Roles'), 'role' => $role, 'roles' => $roles, 'authrole' => $authrole, 'permissions' => App::permissions()];
    }

    /**
     * @Request({"id": "int", "name", "permissions": "array"}, csrf=true)
     * @Response("json")
     */
    public function saveAction($id, $name = '', $permissions = [])
    {
        // is new ?
        if (!$role = Role::find($id)) {
            $role = new Role;
        }

        if ($name !== '') {
            $role->setName($name);
        }

        $role->setPermissions($permissions);
        Role::save($role);

        return App::request()->isXmlHttpRequest() ? ['message' =>__('Roles saved!')] : $this->redirect('@system/role', ['id' => isset($role) ? $role->getId() : 0]);
    }

    /**
     * @Request({"id": "int"}, csrf=true)
     */
    public function deleteAction($id = 0)
    {
        if ($role = Role::find($id)) {
            Role::delete($role);
        }

        return $this->redirect('@system/role');
    }

    /**
     * @Request({"order": "array"}, csrf=true)
     * @Response("json")
     */
    public function priorityAction($order)
    {
        foreach ($order as $id => $priority) {

            $role = Role::find($id);

            if ($role) {
                Role::save($role, compact('priority'));
            }
        }

        return $order;
    }
}
