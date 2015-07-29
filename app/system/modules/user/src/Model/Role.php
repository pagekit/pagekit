<?php

namespace Pagekit\User\Model;

/**
 * @Entity(tableClass="@system_role")
 */
class Role implements RoleInterface, \JsonSerializable
{
    use RoleModelTrait;

    /** @Column(type="integer") @Id */
    protected $id;

    /** @Column(type="string") */
    protected $name;

    /** @Column(type="integer") */
    protected $priority = 0;

    /** @Column(type="simple_array") */
    protected $permissions = [];

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
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * {@inheritdoc}
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

    /**
     * {@inheritdoc}
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * {@inheritdoc}
     */
    public function setPermissions($permissions)
    {
        return $this->permissions = $permissions;
    }

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

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        $data = [
            'isLocked' => $this->isLocked(),
            'isAnonymous' => $this->isAnonymous(),
            'isAuthenticated' => $this->isAuthenticated(),
            'isAdministrator' => $this->isAdministrator()
        ];

        return $this->toJson($data);
    }
}
