<?php

namespace Pagekit\User\Controller;

use Pagekit\Application as App;
use Pagekit\Kernel\Exception\NotFoundException;
use Pagekit\User\Entity\Role;
use Pagekit\User\Entity\User;

/**
 * @Access(admin=true)
 */
class UserController
{
    /**
     * @Access("user: manage users")
     * @Request({"filter": "array", "page":"int"})
     */
    public function indexAction($filter = null, $page = 0)
    {
        $roles = $this->getRoles();
        unset($roles[Role::ROLE_AUTHENTICATED]);

        return [
            '$view' => [
                'title' => __('Users'),
                'name'  => 'system/user:views/admin/index.php'
            ],
            '$data' => [
                'statuses' => User::getStatuses(),
                'roles' => array_values($roles),
                'config' => [
                    'emailVerification' => App::module('system/user')->config('require_verification'),
                    'filter' => $filter,
                    'page' => $page
                ]
            ]
        ];
    }

    /**
     * @Access("user: manage users")
     * @Request({"id": "int"})
     */
    public function editAction($id = 0)
    {
        if (!$id) {

            $user = new User;
            $user->setRoles([Role::find(Role::ROLE_AUTHENTICATED)]);

        } else if (!$user = User::find($id)) {
            throw new NotFoundException(__('User not found.'));
        }

        $roles = App::user()->hasAccess('user: manage user permissions') ? $this->getRoles($user) : false;
        $user->setRoles(null);

        return [
            '$view' => [
                'title' => $id ? __('Edit User') : __('Add User'),
                'name'  => 'system/user:views/admin/edit.php'
            ],
            '$data' => [
                'user'     => $user,
                'statuses' => User::getStatuses(),
                'roles'    => array_values($roles),
                'config'  => [
                    'emailVerification' => App::module('system/user')->config('require_verification'),
                    'currentUser'       => App::user()->getId()
                ]
            ]
        ];
    }

    /**
     * @Access("user: manage user permissions")
     */
    public function permissionsAction()
    {
        return [
            '$view' => [
                'title' => __('Permissions'),
                'name'  => 'system/user:views/admin/permission.php'
            ],
            '$data' => [
                'permissions' => App::module('system/user')->getPermissions(),
                'roles'       => array_values(Role::query()->orderBy('priority')->get())
            ]
        ];
    }

    /**
     * @Access("user: manage user permissions")
     * @Request({"id": "int"})
     */
    public function rolesAction($id = null)
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
                'roles'       => array_values(Role::query()->orderBy('priority')->get())
            ]
        ];
    }

    /**
     * @Access("user: manage settings")
     */
    public function settingsAction()
    {
        return [
            '$view' => [
                'title' => __('User Settings'),
                'name'  => 'system/user:views/admin/settings.php'
            ],
            '$data' => [
                'config' => App::module('system/user')->config()
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
        $roles = [];

        foreach (Role::where(['id <> ?'], [Role::ROLE_ANONYMOUS])->orderBy('priority')->get() as $role) {

            $r = $role->jsonSerialize();

            if ($role->isAuthenticated()) {
                $r['disabled'] = true;
            }

            if ($user && $user->getId() == App::user()->getId() && $user->isAdministrator() && $role->isAdministrator()) {
                $r['disabled'] = true;
            }

            if ($user && $user->hasRole($role)) {
                $r['selected'] = true;
            }

            $roles[$r['id']] = $r;
        }

        return $roles;
    }
}
