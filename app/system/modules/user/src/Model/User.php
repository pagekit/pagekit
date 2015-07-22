<?php

namespace Pagekit\User\Model;

use Pagekit\Application\Exception;
use Pagekit\System\Model\DataTrait;

/**
 * @Entity(tableClass="@system_user", eventPrefix="user")
 */
class User implements UserInterface, \JsonSerializable
{
    use DataTrait, UserModelTrait;

    /** @Column(type="integer") @Id */
    protected $id;

    /** @Column */
    protected $username = '';

    /** @Column */
    protected $password = '';

    /** @Column */
    protected $email = '';

    /** @Column */
    protected $url = '';

    /** @Column(type="datetime") */
    protected $registered;

    /** @Column(type="integer") */
    protected $status = User::STATUS_ACTIVE;

    /** @Column */
    protected $name;

    /** @Column(type="datetime") */
    protected $access;

    /** @Column(type="datetime") */
    protected $login;

    /** @Column */
    protected $activation;

    /** @Column(type="json_array") */
    protected $data;

    /** @ManyToMany(targetEntity="Role", keyFrom="id", keyTo="id", tableThrough="@system_user_role", keyThroughFrom="user_id", keyThroughTo="role_id") */
    protected $roles;

    /**
     * @var array
     */
    protected $permissions;

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the user's id.
     *
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Sets the user's password.
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Sets the user's username
     *
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getStatusText()
    {
        $statuses = self::getStatuses();

        return isset($statuses[$this->status]) ? $statuses[$this->status] : __('Unknown');
    }

    public static function getStatuses()
    {
        return [
            self::STATUS_ACTIVE => __('Active'),
            self::STATUS_BLOCKED => __('Blocked')
        ];
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getRegistered()
    {
        return $this->registered;
    }

    public function setRegistered(\DateTime $registered)
    {
        $this->registered = $registered;
    }

    public function getLogin()
    {
        return $this->login;
    }

    public function setLogin(\DateTime $login)
    {
        $this->login = $login;
    }

    public function getAccess()
    {
        return $this->access;
    }

    public function setAccess(\DateTime $access)
    {
        $this->access = $access;
    }

    public function getActivation()
    {
        return $this->activation;
    }

    public function setActivation($activation)
    {
        $this->activation = $activation;
    }

    /**
     * @return RoleInterface[]
     * @throws \Exception
     */
    public function getRoles()
    {
        if (null === $this->roles) {
            throw new \Exception('Unable to retrieve roles. You\'ll need to add the roles to this user first.');
        }

        return $this->roles;
    }

    /**
     * Sets the user's roles.
     *
     * @param RoleInterface[] $roles
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;
    }

    /**
     * Check if the user has a role.
     *
     * @param  RoleInterface|int $role
     * @return boolean
     */
    public function hasRole($role)
    {
        $id = $role instanceof RoleInterface ? $role->getId() : $role;

        foreach ($this->getRoles() as $role) {
            if ($id == $role->getId()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the user has the anonymous role.
     *
     * @return boolean
     */
    public function isAnonymous()
    {
        return $this->hasRole(RoleInterface::ROLE_ANONYMOUS);
    }

    /**
     * Check if the user has the authenticated role.
     *
     * @return boolean
     */
    public function isAuthenticated()
    {
        return $this->hasRole(RoleInterface::ROLE_AUTHENTICATED);
    }

    /**
     * Check if the user has the administrator role.
     *
     * @return boolean
     */
    public function isAdministrator()
    {
        return $this->hasRole(RoleInterface::ROLE_ADMINISTRATOR);
    }

    /**
     * Check if the user is active.
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->getStatus() == self::STATUS_ACTIVE;
    }

    /**
     * Check if the user is blocked.
     *
     * @return bool
     */
    public function isBlocked()
    {
        return $this->getStatus() == self::STATUS_BLOCKED;
    }

    /**
     * Check if the user has access for a provided permission identifier
     *
     * @param  string  $permission
     * @return boolean
     */
    public function hasPermission($permission)
    {
        if (null === $this->permissions) {

            $this->permissions = [];

            foreach ($this->getRoles() as $role) {
                $this->permissions = array_merge($this->permissions, $role->getPermissions());
            }
        }

        return in_array($permission, $this->permissions);
    }

    /**
     * Check if the user has access for a provided access expression.
     *
     * Expression forms:
     *   - a single permission string starting with a letter and consisting of letters, numbers and characters .:-_ and whitespace
     *   - a boolean expression with multiple permissions and operators like &&, || and (...) parenthesis
     *
     * Examples:
     *   - a single permission string can be "create_posts", "create posts", "posts:create" etc.
     *   - a boolean expression with multiple permissions boolean expression can be "create_posts && delete_posts", "(create posts && delete posts) || manage posts" etc.
     *
     * @param  string $expression
     * @throws \InvalidArgumentException
     * @return boolean
     */
    public function hasAccess($expression)
    {
        $user = $this;

        if ($this->isAdministrator() || empty($expression)) {
            return true;
        }

        if (!preg_match('/[&\(\)\|\!]/', $expression)) {
            return $this->hasPermission($expression);
        }

        $exp = preg_replace('/[^01&\(\)\|!]/', '', preg_replace_callback('/[a-z_][a-z-_\.:\d\s]*/i', function($permission) use ($user) {
            return (int) $user->hasPermission(trim($permission[0]));
        }, $expression));

        if (!$fn = @create_function("", "return $exp;")) {
            throw new \InvalidArgumentException(sprintf('Unable to parse the given access string "%s"', $expression));
        }

        return (bool) $fn();
    }

    public function validate()
    {
        if (empty($this->name)) {
            throw new Exception(__('Name required.'));
        }

        if (empty($this->password)) {
            throw new Exception(__('Password required.'));
        }

        if (strlen($this->username) < 3 || !preg_match('/^[a-zA-Z0-9_\-]+$/', $this->username)) {
            throw new Exception(__('Username is invalid.'));
        }

        // TODO: email validation differs from email validation in vuejs
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception(__('Email is invalid.'));
        }

        if (self::where(['id <> :id'], ['id' => $this->id ?: 0])->where(function ($query) {
            $query->orWhere(['username = :username', 'email = :username'], ['username' => $this->username]);
        })->first()
        ) {
            throw new Exception(__('Username not available.'));
        }

        if (self::where(['id <> :id'], ['id' => $this->id ?: 0])->where(function ($query) {
            $query->orWhere(['username = :email', 'email = :email'], ['email' => $this->email]);
        })->first()
        ) {
            throw new Exception(__('Email not available.'));
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        $user = $this->toJson();

        unset($user['password']);
        unset($user['activation']);

        if ($user['roles']) {
            $user['roles'] = array_map(function($role) { return (string) $role->getId(); }, $user['roles']);
        }

        return $user;
    }

    /**
     * Save related user roles.
     *
     * @PostSave
     */
    public function postSave()
    {
        if (is_array($this->roles)) {
            self::getConnection()->transactional(function ($connection) {

                $connection->delete('@system_user_role', ['user_id' => $this->getId()]);

                if (!array_key_exists(Role::ROLE_AUTHENTICATED, $this->roles)) {
                    $this->roles[Role::ROLE_AUTHENTICATED] = self::getManager()->find('Pagekit\User\Model\Role', Role::ROLE_AUTHENTICATED);
                }

                foreach ($this->roles as $role) {
                    $connection->insert('@system_user_role', ['user_id' => $this->getId(), 'role_id' => $role->getId()]);
                }

            });
        }
    }

    /**
     * Delete all orphaned user role relations.
     *
     * @PostDelete
     */
    public function postDelete()
    {
        self::getConnection()->delete('@system_user_role', ['user_id' => $this->getId()]);
    }
}
