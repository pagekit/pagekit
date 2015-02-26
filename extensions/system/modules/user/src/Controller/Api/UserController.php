<?php

namespace Pagekit\User\Controller\Api;

use Pagekit\Application as App;
use Pagekit\Application\Exception;
use Pagekit\Database\Connection;
use Pagekit\User\Entity\Role;
use Pagekit\User\Entity\User;

/**
 * @Access("system: manage users")
 * @Response("json")
 */
class UserController
{
    /**
     * @Route("/", methods="GET")
     * @Request({"filter": "array", "page":"int"})
     */
    public function indexAction($filter = [], $page = 0)
    {
        $query  = User::query();
        $filter = array_merge(['status' => '', 'search' => '', 'permission' => '', 'role' => ''], $filter);
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

        if ($permission) {
            $sql = $this->getPermissionSql($permission);
            $query->whereExists(function ($query) use ($sql) {
                $query->from('@system_user_role ur')
                    ->join('@system_role r', 'ur.role_id = r.id')
                    ->where(['@system_user.id = ur.user_id', $sql]);
            });
        }

        $limit = App::module('system/user')->config('users_per_page');
        $count = $query->count();
        $pages = ceil($count / $limit);
        $page  = max(0, min($pages - 1, $page));

        return ['users' => array_values($query->offset($page * $limit)->limit($limit)->related('roles')->orderBy('name')->get()), 'pages' => $pages];
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
        try {

            // is new ?
            if (!$user = User::find($id)) {

                if ($id) {
                    throw new Exception(__('User not found.'));
                }

                if (!$password) {
                    throw new Exception(__('Password required.'));
                }

                $user = new User;
                $user->setRegistered(new \DateTime);
            }

            $self = App::user()->getId() == $user->getId();

            if ($self && @$data['status'] == User::STATUS_BLOCKED) {
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

            if (User::where(['id <> :id',], compact('id'))->where(function ($query) use ($name) {
                $query->orWhere(['username = :username', 'email = :username'], ['username' => $name]);
            })->first()
            ) {
                throw new Exception(__('Username not available.'));
            }

            if (User::where(['id <> :id'], compact('id'))->where(function ($query) use ($email) {
                $query->orWhere(['username = :email', 'email = :email'], ['email' => $email]);
            })->first()
            ) {
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

            if (null !== $roles && App::user()->hasAccess('system: manage user permissions')) {

                if ($self && $user->hasRole(Role::ROLE_ADMINISTRATOR) && (!$roles || !in_array(Role::ROLE_ADMINISTRATOR, $roles))) {
                    $roles[] = Role::ROLE_ADMINISTRATOR;
                }

                $user->setRoles($roles ? Role::query()->whereIn('id', $roles)->get() : []);
            }

            unset($data['access'], $data['login'], $data['registered']);

            $user->save($data);

            return ['message' => $id ? __('User saved.') : __('User created.'), 'user' => $user];

        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * @Route("/{id}", methods="DELETE", requirements={"id"="\d+"})
     * @Request({"id": "int"}, csrf=true)
     */
    public function deleteAction($id)
    {
        try {

            if (App::user()->getId() == $id) {
                throw new Exception(__('Unable to delete yourself.'));
            }

            if ($user = User::find($id)) {
                $user->delete();
            }

        } catch (Exception $e) {
            return ['message' => $e->getMessage(), 'error' => true];
        }

        return ['message' => __('Success')];
    }

    /**
     * @Route("/bulk", methods="POST")
     * @Request({"users": "json"}, csrf=true)
     */
    public function bulkSaveAction($users = [])
    {
        // -TODO-

        return User::findAll();
    }

    /**
     * @Route("/bulk", methods="DELETE")
     * @Request({"ids": "json"}, csrf=true)
     */
    public function bulkDeleteAction($ids = [])
    {
        foreach (array_filter($ids) as $id) {
            $this->deleteAction($id);
        }

        return User::findAll();
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
