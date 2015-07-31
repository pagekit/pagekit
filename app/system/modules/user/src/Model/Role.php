<?php

namespace Pagekit\User\Model;

/**
 * @Entity(tableClass="@system_role")
 */
class Role implements \JsonSerializable
{
    use RoleModelTrait;

    /**
     * The identifier of the anonymous role.
     *
     * @var int
     */
    const ROLE_ANONYMOUS = 1;

    /**
     * The identifier of the authenticated role.
     *
     * @var int
     */
    const ROLE_AUTHENTICATED = 2;

    /**
     * The identifier of the administrator role.
     *
     * @var int
     */
    const ROLE_ADMINISTRATOR = 3;

    /** @Column(type="integer") @Id */
    public $id;

    /** @Column(type="string") */
    public $name;

    /** @Column(type="integer") */
    public $priority = 0;

    /** @Column(type="simple_array") */
    public $permissions = [];

    /** @var array */
    protected static $properties = [
        'locked' => 'isLocked',
        'anonymous' => 'isAnonymous',
        'authenticated' => 'isAuthenticated',
        'administrator' => 'isAdministrator'
    ];

    /**
     * {@inheritdoc}
     */
    public function hasPermission($permission)
    {
        return in_array($permission, $this->permissions);
    }

    /**
     * {@inheritdoc}
     */
    public function addPermission($permission)
    {
        $this->permissions[] = (string) $permission;
    }

    /**
     * {@inheritdoc}
     */
    public function clearPermissions()
    {
        $this->permissions = [];
    }

    /**
     * {@inheritdoc}
     */
    public function isLocked()
    {
        return in_array($this->id, [self::ROLE_ANONYMOUS, self::ROLE_AUTHENTICATED, self::ROLE_ADMINISTRATOR]);
    }

    /**
     * {@inheritdoc}
     */
    public function isAnonymous()
    {
        return $this->id == self::ROLE_ANONYMOUS;
    }

    /**
     * {@inheritdoc}
     */
    public function isAuthenticated()
    {
        return $this->id == self::ROLE_AUTHENTICATED;
    }

    /**
     * {@inheritdoc}
     */
    public function isAdministrator()
    {
        return $this->id == self::ROLE_ADMINISTRATOR;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString() {
        return (string) $this->name;
    }
}
