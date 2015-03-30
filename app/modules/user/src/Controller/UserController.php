<?php

namespace Pagekit\User\Controller;

use Pagekit\Application as App;
use Pagekit\User\Entity\Role;
use Pagekit\User\Entity\User;

/**
 * @Access("system: manage users", admin=true)
 */
class UserController
{
    /**
     * @Request({"filter": "array", "page":"int"})
     * @Response("system/user: views/admin/index.php")
     */
    public function indexAction($filter = null, $page = 0)
    {
        $roles = $this->getRoles();
        unset($roles[Role::ROLE_AUTHENTICATED]);

        return [
            '$meta' => [
                'title' => __('Users')
            ],
            '$config' => [
                'emailVerification' => App::option('system:user.require_verification'),
                'filter'            => $filter,
                'page'              => $page
            ],
            '$data' => [
                'statuses'    => User::getStatuses(),
                'permissions' => App::permissions(),
                'roles'       => array_values($roles)
            ]
        ];
    }

    /**
     * @Request({"id": "int"})
     * @Response("system/user: views/admin/edit.php")
     */
    public function editAction($id = 0)
    {
        if (!$user = User::find($id)) {
            $user = new User;
            $user->setRoles([Role::find(Role::ROLE_AUTHENTICATED)]);
        }

        $roles = App::user()->hasAccess('system: manage user permissions') ? $this->getRoles($user) : false;
        $user->setRoles(null);

        return [
            '$meta' => [
                'title' => $id ? __('Edit User') : __('Add User')
            ],
            '$config' => [
                'emailVerification' => App::option('system:user.require_verification'),
                'currentUser'       => App::user()->getId()
            ],
            '$data' => [
                'user'     => $user,
                'statuses' => User::getStatuses(),
                'roles'    => array_values($roles)
            ]
        ];
    }

    /**
     * Gets the user roles.
     *
     * @param  User $user
     * @return array
     */
    protected function getRoles(User $user = null)
    {
        $roles = Role::where(['id <> ?'], [Role::ROLE_ANONYMOUS])->orderBy('priority')->get();

        foreach ($roles as $role) {

            if ($role->isAuthenticated()) {
                $role->disabled = true;
            }

            if ($user && $user->getId() == App::user()->getId() && $user->isAdministrator() && $role->isAdministrator()) {
                $role->disabled = true;
            }

            if ($user && $user->hasRole($role)) {
                $role->selected = true;
            }
        }

        return $roles;
    }
}
