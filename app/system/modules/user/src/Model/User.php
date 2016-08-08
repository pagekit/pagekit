<?php

namespace Pagekit\User\Model;

use Pagekit\Application\Exception;
use Pagekit\Auth\UserInterface;
use Pagekit\System\Model\DataModelTrait;

/**
 * @Entity(tableClass="@system_user")
 */
class User implements UserInterface, \JsonSerializable
{
    use AccessModelTrait, DataModelTrait, UserModelTrait;

    /**
     * The blocked status.
     *
     * @var int
     */
    const STATUS_BLOCKED = 0;

    /**
     * The active status.
     *
     * @var int
     */
    const STATUS_ACTIVE = 1;

    /** @Column(type="integer") @Id */
    public $id;

    /** @Column */
    public $username = '';

    /** @Column */
    public $password = '';

    /** @Column */
    public $email = '';

    /** @Column */
    public $url = '';

    /** @Column(type="datetime") */
    public $registered;

    /** @Column(type="integer") */
    public $status = User::STATUS_ACTIVE;

    /** @Column */
    public $name;

    /** @Column(type="datetime") */
    public $login;

    /** @Column */
    public $activation;

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
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword()
    {
        return $this->password;
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

    /**
     * Check if the user has the anonymous role.
     *
     * @return boolean
     */
    public function isAnonymous()
    {
        return $this->hasRole(Role::ROLE_ANONYMOUS);
    }

    /**
     * Check if the user has the authenticated role.
     *
     * @return boolean
     */
    public function isAuthenticated()
    {
        return $this->hasRole(Role::ROLE_AUTHENTICATED);
    }

    /**
     * Check if the user has the administrator role.
     *
     * @return boolean
     */
    public function isAdministrator()
    {
        return $this->hasRole(Role::ROLE_ADMINISTRATOR);
    }

    /**
     * Check if the user is active.
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->status == self::STATUS_ACTIVE;
    }

    /**
     * Check if the user is blocked.
     *
     * @return bool
     */
    public function isBlocked()
    {
        return $this->status == self::STATUS_BLOCKED;
    }

    /**
     * Check if the user has access for a provided permission identifier
     *
     * @param  string  $permission
     * @return boolean
     */
    public function hasPermission($permission)
    {
        if ($this->permissions === null) {

            $this->permissions = [];
            foreach (self::findRoles($this) as $role) {
                $this->permissions = array_merge($this->permissions, $role->permissions);
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

        if (!preg_match('/^[a-zA-Z0-9._\-]{3,}$/', $this->username)) {
            throw new Exception(__('Username is invalid.'));
        }

        // TODO: email validation differs from email validation in vuejs
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception(__('Email is invalid.'));
        }

        if (self::where(['id <> :id'], ['id' => $this->id ?: 0])->where(function ($query) {
            $query->orWhere(['LOWER(username) = :username', 'LOWER(email) = :username'], ['username' => strtolower($this->username)]);
        })->first()
        ) {
            throw new Exception(__('Username not available.'));
        }

        if (self::where(['id <> :id'], ['id' => $this->id ?: 0])->where(function ($query) {
            $query->orWhere(['LOWER(username) = :email', 'LOWER(email) = :email'], ['email' => strtolower($this->email)]);
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
        return $this->toArray([], ['password', 'activation']);
    }
}
