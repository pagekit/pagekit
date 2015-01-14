<?php

namespace Pagekit\User\Controller;

use Pagekit\Framework\Application as App;
use Pagekit\Framework\Controller\Exception;
use Pagekit\User\Entity\Role;
use Pagekit\User\Entity\User;

/**
 * @Access("system: manage users", admin=true)
 */
class UserController
{
    const USERS_PER_PAGE = 20;

    /**
     * @Request({"filter": "array", "page":"int"})
     * @Response("extensions/system/views/admin/user/index.razr")
     */
    public function indexAction($filter = null, $page = 0)
    {
        if ($filter) {
            App::session()->set('user.filter', $filter);
        } else {
            $filter = App::session()->get('user.filter', []);
        }

        $query = User::query();

        if (isset($filter['status'])) {
            if (is_numeric($filter['status'])) {
                $filter['status'] = (int) $filter['status'];
                $query->where(['status' => intval($filter['status'])]);
                if (!$filter['status']) {
                    $query->where('access IS NOT NULL');
                }
            } elseif ('new' == $filter['status']) {
                $query->where(['status' => User::STATUS_BLOCKED, 'access IS NULL']);
            }
        }

        if (isset($filter['search']) && strlen($filter['search'])) {
            $query->where(function($query) use ($filter) {
                $query->orWhere(['username LIKE :search', 'name LIKE :search', 'email LIKE :search'], ['search' => "%{$filter['search']}%"]);
            });
        }

        $role = isset($filter['role']) && is_numeric($filter['role']) ? intval($filter['role']) : null;
        $permission = isset($filter['permission']) && strlen($filter['permission']) ? $filter['permission'] : null;

        if ($role || $permission) {

            if ($role) {
                $query->whereExists(function($query) use ($role) {
                    $query->from('@system_user_role u')
                          ->where(['@system_user.id = u.user_id', 'u.role_id' => $role]);
                });
            }

            if ($permission) {
                $sql = $this->getPermissionSql($permission);
                $query->whereExists(function($query) use ($sql) {
                    $query->from('@system_user_role ur')
                        ->join('@system_role r', 'ur.role_id = r.id')
                        ->where(['@system_user.id = ur.user_id', $sql]);
                });
            }
        }

        $limit = self::USERS_PER_PAGE;
        $count = $query->count();
        $total = ceil($count / $limit);
        $page  = max(0, min($total - 1, $page));

        $users = $query->offset($page * $limit)->limit($limit)->related('roles')->orderBy('name')->get();
        $roles = $this->getRoles();

        if (App::request()->isXmlHttpRequest()) {
            return App::response()->json([
                'table' => App::view()->render('extensions/system/views/admin/user/table.razr', ['users' => $users]),
                'total' => $total
            ]);
        }

        return ['head.title' => __('Users'), 'users' => $users, 'statuses' => User::getStatuses(), 'roles' => $roles, 'permissions' => App::permissions(), 'filter' => $filter, 'total' => $total];
    }

    /**
     * @Response("extensions/system/views/admin/user/edit.razr")
     */
    public function addAction()
    {
        $user = new User;
        $user->setRoles([]);

        $roles = App::user()->hasAccess('administer permissions') ? $this->getRoles() : [];

        return ['head.title' => __('Add User'), 'user' => $user, 'roles' => $roles];
    }

    /**
     * @Request({"id": "int"})
     * @Response("extensions/system/views/admin/user/edit.razr")
     */
    public function editAction($id)
    {
        $user  = User::where(compact('id'))->related('roles')->first();
        $roles = App::user()->hasAccess('system: manage user permissions') ? $this->getRoles($user) : [];

        return ['head.title' => __('Edit User'), 'user' => $user, 'roles' => $roles];
    }

    /**
     * @Request({"id": "int", "user": "array", "password", "roles": "array"}, csrf=true)
     * @Response("json")
     */
    public function saveAction($id, $data, $password, $roles = null)
    {
        try {

            // is new ?
            if (!$user = User::find($id)) {

                if ($id) {
                    throw new Exception(__('User not found.'));
                }

                if (empty($password)) {
                    throw new Exception(__('Password required.'));
                }

                $user = new User;
                $user->setRegistered(new \DateTime);
            }

            $self = App::user()->getId() == $user->getId();

            if ($self && $user->isBlocked()) {
                throw new Exception(__('Unable to block yourself.'));
            }

            $name  = trim(@$data['username']);
            $email = trim(@$data['email']);

            if (strlen($name) < 3 || !preg_match('/^[a-zA-Z0-9_\-]+$/', $name)) {
                throw new Exception(__('Username is invalid.'));
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception(__('Email is invalid.'));
            }

            if (User::where(['id <> :id', ], compact('id'))->where(function($query) use ($name) {
                $query->orWhere(['username = :username', 'email = :username'], ['username' => $name]);
            })->first()) {
                throw new Exception(__('Username not available.'));
            }

            if (User::where(['id <> :id'], compact('id'))->where(function($query) use ($email) {
                $query->orWhere(['username = :email', 'email = :email'], ['email' => $email]);
            })->first()) {
                throw new Exception(__('Email not available.'));
            }

            $data['username'] = $name;
            $data['email']    = $email;

            if ($email != $user->getEmail()) {
                $user->set('verified', false);
            }

            if (!empty($password)) {
                $user->setPassword(App::get('auth.password')->hash($password));
            }

            if (App::user()->hasAccess('system: manage user permissions')) {

                if ($self && $user->hasRole(Role::ROLE_ADMINISTRATOR) && (!$roles || !in_array(Role::ROLE_ADMINISTRATOR, $roles))) {
                    $roles[] = Role::ROLE_ADMINISTRATOR;
                }

                $user->setRoles($roles ? Role::query()->whereIn('id', $roles)->get() : []);
            }

            User::save($user, $data);

            return ['message' => $id ? __('User saved.') : __('User created.'), 'user' => $this->getInfo($user)];

        } catch (Exception $e) {

            return ['error' => $e->getMessage()];
        }
    }

    /**
     * @Request({"ids": "int[]"}, csrf=true)
     * @Response("json")
     */
    public function deleteAction($ids = [])
    {
        if (in_array(App::user()->getId(), $ids)) {
            return ['message' => __('Unable to delete yourself.'), 'error' => true];
        }

        foreach ($ids as $key => $id) {
            if ($user = User::find($id)) {
                User::delete($user);
            }
        }

        return ['message' => _c('{1} User deleted.|]1,Inf[ Users deleted.', count($ids))];
    }

    /**
     * @Request({"status": "int", "ids": "int[]"}, csrf=true)
     * @Response("json")
     */
    public function statusAction($status, $ids = [])
    {
        if ($status == User::STATUS_BLOCKED && in_array(App::user()->getId(), $ids)) {
            return ['message' => __('Unable to block yourself.'), 'error' => true];
        }

        foreach ($ids as $id) {
            if ($user = User::find($id)) {

                $user->setActivation('');

                if ($status != $user->getStatus()) {
                    User::save($user, compact('status'));
                }
            }
        }

        if ($status == User::STATUS_BLOCKED) {
            $message = _c('{1} User blocked.|]1,Inf[ Users blocked.', count($ids));
        } else {
            $message = _c('{1} User activated.|]1,Inf[ Users activated.', count($ids));
        }

        return ['message' => $message];
    }

    /**
     * Gets the user info.
     *
     * @param  User $user
     * @return array
     */
    protected function getInfo(User $user)
    {
        return [
            'id'         => $user->getId(),
            'username'   => $user->getUsername(),
            'name'       => $user->getName(),
            'email'      => $user->getEmail(),
            'status'     => $user->getStatusText(),
            'badge'      => $user->getStatus() ? 'success' : 'danger',
            'new'        => $user->isNew(),
            'login'      => ($date = $user->getLogin()) ? App::dates()->format($date) : __('Never'),
            'registered' => ($date = $user->getRegistered()) ? App::dates()->format($date) : null
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
        }

        return $roles;
    }

    /**
     * Gets the permission SQL.
     *
     * @param  string $permission
     * @return string
     */
    protected function getPermissionSql($permission)
    {
        $expr     = App::db()->getExpressionBuilder();
        $platform = App::db()->getDatabasePlatform();
        $col      = $platform->getConcatExpression(App::db()->quote(','), 'r.permissions', App::db()->quote(','));

        $params = [
            $expr->eq('r.id', Role::ROLE_ADMINISTRATOR),
            $expr->comparison($col, 'LIKE', App::db()->quote("%,$permission,%"))
        ];

        return (string) call_user_func_array([$expr, 'orX'], $params);
    }
}
