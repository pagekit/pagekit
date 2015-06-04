<?php

namespace Pagekit\User\Controller;

use Pagekit\Application as App;
use Pagekit\Database\Connection;
use Pagekit\User\Entity\Role;
use Pagekit\User\Entity\User;

/**
 * @Access("user: manage users")
 */
class UserApiController
{
    /**
     * @Route("/", methods="GET")
     * @Request({"filter": "array", "page":"int", "limit":"int"})
     */
    public function indexAction($filter = [], $page = 0, $limit = 0)
    {
        $query  = User::query();
        $filter = array_merge(array_fill_keys(['status', 'search', 'role', 'order', 'access'], ''), $filter);
        extract($filter, EXTR_SKIP);

        if (is_numeric($status)) {
            $query->where(['status' => (int) $status]);
            if (!$status) {
                $query->where('access IS NOT NULL');
            }
        } elseif ('new' == $status) {
            $query->where(['status' => User::STATUS_BLOCKED, 'access IS NULL']);
        }

        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->orWhere(['username LIKE :search', 'name LIKE :search', 'email LIKE :search'], ['search' => "%{$search}%"]);
            });
        }

        if ($role) {
            $query->whereExists(function ($query) use ($role) {
                $query->from('@system_user_role ur')
                    ->where(['@system_user.id = ur.user_id', 'ur.role_id' => (int) $role]);
            });
        }

        if ($access) {
            $query->where('access > ?', [date('Y-m-d H:i:s', time() - max(0, (int) $access))]);
        }

        if (!preg_match('/^(name|email)\s(asc|desc)$/i', $order, $order)) {
            $order = [1 => 'name', 2 => 'asc'];
        }

        $default = App::module('system/user')->config('users_per_page');
        $limit   = min(max(0, $limit), $default) ?: $default;
        $count   = $query->count();
        $pages   = ceil($count / $limit);
        $page    = max(0, min($pages - 1, $page));
        $users   = array_values($query->offset($page * $limit)->limit($limit)->related('roles')->orderBy($order[1], $order[2])->get());

        return compact('users', 'pages', 'count');
    }

    /**
     * @Route("/{id}", methods="GET", requirements={"id"="\d+"})
     */
    public function getAction($id)
    {
        return User::find($id);
    }

    /**
     * @Route("/", methods="POST")
     * @Route("/{id}", methods="POST", requirements={"id"="\d+"})
     * @Request({"user": "array", "password", "roles": "array", "id": "int"}, csrf=true)
     */
    public function saveAction($data, $password = null, $roles = null, $id = 0)
    {
        // is new ?
        if (!$user = User::find($id)) {

            if ($id) {
                App::abort(400, __('User not found.'));
            }

            if (!$password) {
                App::abort(400, __('Password required.'));
            }

            $user = new User;
            $user->setRegistered(new \DateTime);
        }

        $self = App::user()->getId() == $user->getId();

        if ($self && @$data['status'] == User::STATUS_BLOCKED) {
            App::abort(400, __('Unable to block yourself.'));
        }

        $name  = trim(@$data['username']);
        $email = trim(@$data['email']);

        if (strlen($name) < 3 || !preg_match('/^[a-zA-Z0-9_\-]+$/', $name)) {
            App::abort(400, __('Username is invalid.'));
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            App::abort(400, __('Email is invalid.'));
        }

        if (User::where(['id <> :id',], compact('id'))->where(function ($query) use ($name) {
            $query->orWhere(['username = :username', 'email = :username'], ['username' => $name]);
        })->first()
        ) {
            App::abort(400, __('Username not available.'));
        }

        if (User::where(['id <> :id'], compact('id'))->where(function ($query) use ($email) {
            $query->orWhere(['username = :email', 'email = :email'], ['email' => $email]);
        })->first()
        ) {
            App::abort(400, __('Email not available.'));
        }

        $data['username'] = $name;
        $data['email']    = $email;

        if ($email != $user->getEmail()) {
            $user->set('verified', false);
        }

        if (!empty($password)) {
            $user->setPassword(App::get('auth.password')->hash($password));
        }

        if (null !== $roles && App::user()->hasAccess('user: manage user permissions')) {

            if ($self && $user->hasRole(Role::ROLE_ADMINISTRATOR) && (!$roles || !in_array(Role::ROLE_ADMINISTRATOR, $roles))) {
                $roles[] = Role::ROLE_ADMINISTRATOR;
            }

            $user->setRoles($roles ? Role::query()->whereIn('id', $roles)->get() : []);
        }

        unset($data['access'], $data['login'], $data['registered']);

        $user->save($data);

        return ['message' => $id ? __('User saved.') : __('User created.'), 'user' => $user];
    }

    /**
     * @Route("/{id}", methods="DELETE", requirements={"id"="\d+"})
     * @Request({"id": "int"}, csrf=true)
     */
    public function deleteAction($id)
    {
        if (App::user()->getId() == $id) {
            App::abort(400, __('Unable to delete yourself.'));
        }

        if ($user = User::find($id)) {
            $user->delete();
        }

        return ['message' => __('Success')];
    }

    /**
     * @Route("/bulk", methods="POST")
     * @Request({"users": "array"}, csrf=true)
     */
    public function bulkSaveAction($users = [])
    {
        foreach ($users as $data) {
            $this->saveAction($data, null, null, isset($data['id']) ? $data['id'] : 0);
        }

        return ['message' => __('Users saved.')];
    }

    /**
     * @Route("/bulk", methods="DELETE")
     * @Request({"ids": "array"}, csrf=true)
     */
    public function bulkDeleteAction($ids = [])
    {
        foreach (array_filter($ids) as $id) {
            $this->deleteAction($id);
        }

        return ['message' => __('Users deleted.')];
    }

    /**
     * Gets the permission SQL.
     *
     * @param  string $permission
     * @return string
     */
    protected function getPermissionSql($permission)
    {
        /** @var Connection $db */
        $db   = App::db();
        $expr = $db->getExpressionBuilder();
        $col  = $db->getDatabasePlatform()->getConcatExpression($db->quote(','), 'r.permissions', $db->quote(','));

        return (string) $expr->orX(
            $expr->eq('r.id', Role::ROLE_ADMINISTRATOR),
            $expr->comparison($col, 'LIKE', App::db()->quote("%,$permission,%"))
        );
    }
}
