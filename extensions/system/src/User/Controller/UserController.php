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
                $sql = $this->getPermissionSql($permission);
                $query->whereExists(function($query) use ($sql) {
                    $query->from('@system_user_role ur')
                        ->join('@system_role r', 'ur.role_id = r.id')
                        ->where(array('@system_user.id = ur.user_id', $sql));
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
        $roles = $this->user->hasAccess('system: manage user permissions') ? $this->getRoles($user) : array();

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

            if (strlen($name) < 3 || !preg_match('/^[a-zA-Z0-9_\-]+$/', $name)) {
                throw new Exception(__('Username is invalid.'));
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
                $user->setPassword($this('auth.password')->hash($password));
            }

            if ($this->user->hasAccess('system: manage user permissions')) {

                if ($self && $user->hasRole(RoleInterface::ROLE_ADMINISTRATOR) && (!$roles || !in_array(RoleInterface::ROLE_ADMINISTRATOR, $roles))) {
                    $roles[] = RoleInterface::ROLE_ADMINISTRATOR;
                }

                $user->setRoles($roles ? $this->roles->query()->whereIn('id', $roles)->get() : array());
            }

            $this->users->save($user, $data);

            if (!$id) {
                $this->sendWelcomeEmail($user);
            }

            $response = array('message' => $id ? __('User saved.') : __('User created.'), 'user' => $this->getInfo($user));

        } catch (Exception $e) {

            $response = array('error' => $e->getMessage());
        }

        return $this('response')->json($response);
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
     * Gets the user info.
     *
     * @param  User $user
     * @return array
     */
    protected function getInfo(User $user)
    {
        return array(
            'id'         => $user->getId(),
            'username'   => $user->getUsername(),
            'name'       => $user->getName(),
            'email'      => $user->getEmail(),
            'status'     => $user->getStatusText(),
            'badge'      => $user->getStatus() ? 'success' : 'danger',
            'login'      => ($date = $user->getLogin()) ? $this('dates')->format($date) : __('Never'),
            'registered' => ($date = $user->getRegistered()) ? $this('dates')->format($date) : null
        );
    }

    /**
     * Gets the user roles.
     *
     * @param  User $user
     * @return array
     */
    protected function getRoles(User $user = null)
    {
        $roles = $this->roles->where(array('id <> ?'), array(Role::ROLE_ANONYMOUS))->orderBy('priority')->get();

        foreach ($roles as $role) {

            if ($role->isAuthenticated()) {
                $role->disabled = true;
            }

            if ($user && $user->getId() == $this('user')->getId() && $user->isAdministrator() && $role->isAdministrator()) {
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
        $expr     = $this('db')->getExpressionBuilder();
        $platform = $this('db')->getDatabasePlatform();
        $col      = $platform->getConcatExpression($this('db')->quote(','), 'r.permissions', $this('db')->quote(','));

        $params = array(
            $expr->eq('r.id', Role::ROLE_ADMINISTRATOR),
            $expr->comparison($col, 'LIKE', $this('db')->quote("%,$permission,%"))
        );

        return (string) call_user_func_array(array($expr, 'orX'), $params);
    }

    /**
     * Sends the user welcome email.
     *
     * @param User $user
     */
    protected function sendWelcomeEmail(User $user)
    {
        if (!$this('config')->get('mail.enabled')) {
            return;
        }

        $this('mailer')->create()
            ->setTo($user->getEmail())
            ->setSubject(__('Welcome!'))
            ->setBody($this('view')->render('system/user/mails/welcome.razr.php', array('name' => $user->getName(), 'username' => $user->getUsername())), 'text/html')
            ->queue();
    }
}
