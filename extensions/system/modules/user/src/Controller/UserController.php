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
     * @Response("extensions/system/modules/user/views/admin/index.php")
     */
    public function indexAction($filter = null, $page = 0)
    {
        $roles = $this->getRoles();
        unset($roles[Role::ROLE_AUTHENTICATED]);

        App::view()->metas(['title' => __('Users')]);
        App::view()->scripts('user-index', 'extensions/system/modules/user/app/index.js', ['vue-system', 'gravatar']);
        App::view()->addData('user', [
                'config' => [
                    'emailVerification' => App::option('system:user.require_verification'),
                    'filter'            => $filter,
                    'page'              => $page
                ],
                'data'   => [
                    'users'       => json_decode(App::router()->call('@api/system/user', compact('filter', 'page'))->getContent()),
                    'statuses'    => User::getStatuses(),
                    'permissions' => App::permissions(),
                    'roles'       => $roles
                ]
            ]);
    }

    /**
     * @Request({"id": "int"})
     * @Response("extensions/system/modules/user/views/admin/edit.php")
     */
    public function editAction($id = 0)
    {
        if (!$user = User::find($id)) {
            $user = new User;
            $user->setRoles([Role::find(Role::ROLE_AUTHENTICATED)]);
        }

        $roles = App::user()->hasAccess('system: manage user permissions') ? $this->getRoles($user) : false;
        $user->setRoles(null);

        App::view()->metas(['title' => $id ? __('Edit User') : __('Add User')]);
        App::view()->scripts('user-edit', 'extensions/system/modules/user/app/edit.js', ['vue-system', 'uikit-form-password', 'gravatar']);
        App::view()->addData('user', [
                'config' => [
                    'emailVerification' => App::option('system:user.require_verification'),
                    'currentUser'       => App::user()->getId()
                ],
                'data'   => [
                    'user'     => $user,
                    'statuses' => User::getStatuses(),
                    'roles'    => array_values($roles)
                ]
            ]);
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
