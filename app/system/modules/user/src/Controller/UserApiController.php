<?php

namespace Pagekit\User\Controller;

use Pagekit\Application as App;
use Pagekit\Application\Exception;
use Pagekit\User\Model\Role;
use Pagekit\User\Model\User;

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

            if ($status) {
                $query->where('login IS NOT NULL');
            }

        } elseif ('new' == $status) {
            $query->where(['status' => User::STATUS_ACTIVE, 'login IS NULL']);
        }

        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->orWhere(['username LIKE :search', 'name LIKE :search', 'email LIKE :search'], ['search' => "%{$search}%"]);
            });
        }

        if ($role) {
            $query->whereInSet('roles', $role);
        }

        if ($access) {
            $query->whereExists(function($query) use ($access) {
                $query
                    ->select('id')->from('@system_auth as a')
                    ->where('a.user_id = @system_user.id')
                    ->where(['a.access > :access', 'a.status > :status'], ['access' => date('Y-m-d H:i:s', time() - max(0, (int) $access)), 'status' => 0]);
            });
        }

        if (preg_match('/^(username|name|email|registered|login)\s(asc|desc)$/i', $order, $match)) {
            $order = $match;
        } else {
            $order = [1=>'username', 2=>'asc'];
        }

        $default = App::module('system/user')->config('users_per_page');
        $limit   = min(max(0, $limit), $default) ?: $default;
        $count   = $query->count();
        $pages   = ceil($count / $limit);
        $page    = max(0, min($pages - 1, $page));
        $users   = array_values($query->offset($page * $limit)->limit($limit)->orderBy($order[1], $order[2])->get());

        return compact('users', 'pages', 'count');
    }

    /**
     * @Request({"filter": "array"})
     */
    public function countAction($filter = [])
    {
        $query  = User::query();
        $filter = array_merge(array_fill_keys(['status', 'search', 'role', 'order', 'access'], ''), (array)$filter);
        extract($filter, EXTR_SKIP);

        if (is_numeric($status)) {

            $query->where(['status' => (int) $status]);

            if ($status) {
                $query->where('login IS NOT NULL');
            }

        } elseif ('new' == $status) {
            $query->where(['status' => User::STATUS_ACTIVE, 'login IS NULL']);
        }

        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->orWhere(['username LIKE :search', 'name LIKE :search', 'email LIKE :search'], ['search' => "%{$search}%"]);
            });
        }

        if ($role) {
            $query->whereInSet('roles', $role);
        }

        if ($access) {
            $query->whereExists(function($query) use ($access) {
                $query
                    ->select('id')->from('@system_auth as a')
                    ->where('a.user_id = @system_user.id')
                    ->where(['a.access > :access', 'a.status > :status'], ['access' => date('Y-m-d H:i:s', time() - max(0, (int) $access)), 'status' => 0]);
            });
        }

        $count = $query->count();

        return compact('count');
    }

    /**
     * @Route("/{id}", methods="GET", requirements={"id"="\d+"})
     */
    public function getAction($id)
    {
        if (!$user = User::find($id)) {
            App::abort(404, 'User not found.');
        }

        return $user;
    }

    /**
     * @Route("/", methods="POST")
     * @Route("/{id}", methods="POST", requirements={"id"="\d+"})
     * @Request({"user": "array", "password", "id": "int"}, csrf=true)
     */
    public function saveAction($data, $password = null, $id = 0)
    {
        try {

            // is new ?
            if (!$user = User::find($id)) {

                if ($id) {
                    App::abort(404, __('User not found.'));
                }

                if (!$password) {
                    App::abort(400, __('Password required.'));
                }

                $user = User::create(['registered' => new \DateTime]);
            }

            $user->name = @$data['name'];
            $user->username = @$data['username'];
            $user->email = @$data['email'];

            $self = App::user()->id == $user->id;
            if ($self && @$data['status'] == User::STATUS_BLOCKED) {
                App::abort(400, __('Unable to block yourself.'));
            }

            if (@$data['email'] != $user->email) {
                $user->set('verified', false);
            }

            if (!empty($password)) {

                if (trim($password) != $password || strlen($password) < 3) {
                    throw new Exception(__('Invalid Password.'));
                }

                $user->password = App::get('auth.password')->hash($password);
            }

            $key    = array_search(Role::ROLE_ADMINISTRATOR, @$data['roles'] ?: []);
            $add    = false !== $key && !$user->isAdministrator();
            $remove = false === $key && $user->isAdministrator();

            if (($self && $remove) || !App::user()->isAdministrator() && ($remove || $add)) {
                App::abort(403, 'Cannot add/remove Admin Role.');
            }

            unset($data['login'], $data['registered']);

            $user->validate();
            $user->save($data);

            return ['message' => 'success', 'user' => $user];

        } catch (Exception $e) {
            App::abort(400, $e->getMessage());
        }
    }

    /**
     * @Route("/{id}", methods="DELETE", requirements={"id"="\d+"})
     * @Request({"id": "int"}, csrf=true)
     */
    public function deleteAction($id)
    {
        if (App::user()->id == $id) {
            App::abort(400, __('Unable to delete yourself.'));
        }

        if ($user = User::find($id)) {
            $user->delete();
        }

        return ['message' => 'success'];
    }

    /**
     * @Route("/bulk", methods="POST")
     * @Request({"users": "array"}, csrf=true)
     */
    public function bulkSaveAction($users = [])
    {
        foreach ($users as $data) {
            $this->saveAction($data, null, isset($data['id']) ? $data['id'] : 0);
        }

        return ['message' => 'success'];
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

        return ['message' => 'success'];
    }
}
