<?php

namespace Pagekit\User\Model;

abstract class User implements UserInterface
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $username = '';

    /**
     * @var string
     */
    protected $password = '';

    /**
     * @var RoleInterface[]
     */
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

        if (empty($expression)) {
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
}
