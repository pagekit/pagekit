<?php

namespace Pagekit\User\Controller;

use Pagekit\Component\Database\ORM\Repository;
use Pagekit\Framework\Controller\Controller;
use Pagekit\Framework\Controller\Exception;
use Pagekit\User\Entity\Role;
use Pagekit\User\Entity\User;
use Pagekit\User\Entity\UserRepository;
use Pagekit\User\Model\RoleInterface;

/**
 * @Access("system: manage users", admin=true)
 */
class UserController extends Controller
{
    /**
     * @var User
     */
    protected $user;

    /**
     * @var UserRepository
     */
    protected $users;

    /**
     * @var Repository
     */
    protected $roles;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->user  = $this('user');
        $this->users = $this('users')->getUserRepository();
        $this->roles = $this('users')->getRoleRepository();
    }

    /**
     * @Request({"filter": "array"})
     * @View("system/admin/user/index.razr.php")
     */
    public function indexAction($filter = null)
    {
        if ($filter) {
            $this('session')->set('user.filter', $filter);
        } else {
            $filter = $this('session')->get('user.filter', array());
        }

        $query = $this->users->query()->related('roles')->orderBy('name');

        if (isset($filter['status']) && is_numeric($filter['status'])) {
            $query->where(array('status' => intval($filter['status'])));
        }

        if (isset($filter['search']) && strlen($filter['search'])) {
            $query->where(function($query) use ($filter) {
                $query->orWhere(array('username LIKE :search', 'name LIKE :search', 'email LIKE :search'), array('search' => "%{$filter['search']}%"));
            });
        }

        $role = isset($filter['role']) && is_numeric($filter['role']) ? intval($filter['role']) : null;
        $permission = isset($filter['permission']) && strlen($filter['permission']) ? $filter['permission'] : null;

        if ($role || $permission) {

            if ($role) {
                $query->whereExists(function($query) use ($role) {
                    $query->from('@system_user_role u')
                          ->where(array('@system_user.id = u.user_id', 'u.role_id' => $role));
                });
            }

            if ($permission) {
                $query->whereExists(function($query) use ($permission) {
                    $query->from('@system_user_role ur')
                        ->join('@system_role r', 'ur.role_id = r.id')
                        ->where(array('@system_user.id = ur.user_id', $this->getPermissionSql($permission)));
                });
            }
        }

        $users = $query->get();
        $roles = $this->getRoles();

        return array('head.title' => __('Users'), 'users' => $users, 'statuses' => User::getStatuses(), 'roles' => $roles, 'permissions' => $this('permissions'), 'filter' => $filter);
    }

    /**
     * @View("system/admin/user/edit.razr.php")
     */
    public function addAction()
    {
        $user = new User;
        $user->setRoles(array());

        $roles = $this->user->hasAccess('administer permissions') ? $this->getRoles() : array();

        return array('head.title' => __('Add User'), 'user' => $user, 'roles' => $roles);
    }

    /**
     * @Request({"id": "int"})
     * @View("system/admin/user/edit.razr.php")
     */
    public function editAction($id)
    {
        $user  = $this->users->where(compact('id'))->related('roles')->first();
        $roles = $this->user->hasAccess('system: manage user permissions') ? $this->getRoles() : array();

        return array('head.title' => __('Edit User'), 'user' => $user, 'roles' => $roles);
    }

    /**
     * @Request({"id": "int", "user": "array", "password", "roles": "array"})
     * @Token
     */
    public function saveAction($id, $data, $password, $roles = null)
    {
        try {

            // is new ?
            if (!$user = $this->users->find($id)) {

                if ($id) {
                    throw new Exception(__('User not found.'));
                }

                if (empty($password)) {
                    throw new Exception(__('Password required.'));
                }

                $user = new User;
                $user->setRegistered(new \DateTime);
            }

            $self = $this->user->getId() == $user->getId();

            if ($self && $user->getStatus() == User::STATUS_BLOCKED) {
                throw new Exception(__('Unable to block yourself.'));
            }

            $name  = trim(@$data['username']);
            $email = trim(@$data['email']);

            if (strlen($name) < 3) {
                throw new Exception(__('Name is invalid.'));
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception(__('Email is invalid.'));
            }

            if ($this->users->where(array('id <> :id', ), compact('id'))->where(function($query) use ($name) {
                $query->orWhere(array('username = :username', 'email = :username'), array('username' => $name));
            })->first()) {
                throw new Exception(__('Username not available.'));
            }

            if ($this->users->where(array('id <> :id'), compact('id'))->where(function($query) use ($email) {
                $query->orWhere(array('username = :email', 'email = :email'), array('email' => $email));
            })->first()) {
                throw new Exception(__('Email not available.'));
            }

            $data['username'] = $name;
            $data['email']    = $email;

            if (!empty($password)) {
                $user->setPassword($this('auth.encoder.native')->hash($password));
            }

            if ($this->user->hasAccess('system: manage user permissions')) {

                if ($self && $user->hasRole(RoleInterface::ROLE_ADMINISTRATOR) && (!$roles || !in_array(RoleInterface::ROLE_ADMINISTRATOR, $roles))) {
                    $roles[] = RoleInterface::ROLE_ADMINISTRATOR;
                }

                $user->setRoles($roles ? $this->roles->query()->whereIn('id', $roles)->get() : array());
            }

            $this->users->save($user, $data);

            if (!$id && $this('config')->get('mail.enabled')) {
                $this('mailer')->create()
                    ->to($user->getEmail())
                    ->from($this('config')->get('mail.from.address'), $this('config')->get('mail.from.name'))
                    ->subject(__('Welcome!'))
                    ->body($this('view')->render('system/user/mails/welcome.razr.php', array('name' => $user->getName(), 'username' => $user->getUsername())), 'text/html')
                    ->queue();
            }

            $this('message')->success($id ? __('User saved.') : __('User created.'));

        } catch (Exception $e) {
            $this('message')->error($e->getMessage());
        }

        if ($user && $user->getId()) {
            return $this->redirect('@system/user/edit', array('id' => $user->getId()));
        }

        return $this->redirect('@system/user/add');
    }

    /**
     * @Request({"ids": "int[]"})
     * @Token
     */
    public function deleteAction($ids = array())
    {
        foreach ($ids as $id) {

            if ($id == $this->user->getId()) {
                $this('message')->warning(__('Unable to delete self.'));
                continue;
            }

            if ($user = $this->users->find($id)) {
                $this->users->delete($user);
            }
        }

        $this('message')->success(_c('{0} No user deleted.|{1} User deleted.|]1,Inf[ Users deleted.', count($ids)));

        return $this->redirect('@system/user/index');
    }

    /**
     * @Request({"status": "int", "ids": "int[]"})
     * @Token
     */
    public function statusAction($status, $ids = array())
    {
        foreach ($ids as $id) {
            if ($user = $this->users->find($id)) {

                $self = $this->user->getId() == $user->getId();

                if ($self && $status == User::STATUS_BLOCKED) {
                    $this('message')->warning(__('Unable to block yourself.'));
                    continue;
                }

                if ($status != $user->getStatus()) {
                    $this->users->save($user, compact('status'));
                }
            }
        }

        return $this->redirect('@system/user/index');
    }

    /**
     * @param  string $permission
     * @return string
     */
    protected function getPermissionSql($permission)
    {
        $expr     = $this('db')->getExpressionBuilder();
        $platform = $this('db')->getDatabasePlatform();
        $col      = $platform->getConcatExpression($this('db')->quote(','), 'r.permissions', $this('db')->quote(','));

        $params = array(
            $expr->eq('r.id', Role::ROLE_ADMINISTRATOR),
            $expr->comparison($col, 'LIKE', $this('db')->quote("%,$permission,%"))
        );

        return (string) call_user_func_array(array($expr, 'orX'), $params);
    }

    protected function getRoles()
    {
        return $this->roles->where(array('id <> ?', 'id <> ?'), array(Role::ROLE_ANONYMOUS, Role::ROLE_AUTHENTICATED))->orderBy('priority')->get();
    }
}
