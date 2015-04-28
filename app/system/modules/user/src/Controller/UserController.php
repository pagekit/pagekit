<?php

namespace Pagekit\User\Controller;

use Pagekit\Application as App;
use Pagekit\Kernel\Exception\NotFoundException;
use Pagekit\User\Entity\Role;
use Pagekit\User\Entity\User;

/**
 * @Access("user: manage users", admin=true)
 * @Route(name="")
 */
class UserController
{
    /**
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
            '$config' => [
                'emailVerification' => App::module('system/user')->config('require_verification'),
                'filter'            => $filter,
                'page'              => $page
            ],
            '$data' => [
                'statuses' => User::getStatuses(),
                'roles'    => array_values($roles)
            ]
        ];
    }

    /**
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
            '$config' => [
                'emailVerification' => App::module('system/user')->config('require_verification'),
                'currentUser'       => App::user()->getId()
            ],
            '$data' => [
                'user'     => $user,
                'statuses' => User::getStatuses(),
                'roles'    => array_values($roles)
            ]
        ];
    }

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
