<?php

namespace Pagekit\User\Model;

class AccessLevel implements AccessLevelInterface
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var int
     */
    protected $priority = 0;

    /**
     * @var array
     */
    protected $roles = array();

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
    public function getRoles()
    {
        return (array) $this->roles;
    }

    /**
     * {@inheritdoc}
     */
    public function setRoles($roles)
    {
        return $this->roles = $roles;
    }

    /**
     * @param  RoleInterface $role
     * @return bool
     */
    public function hasRole(RoleInterface $role)
    {
        return in_array($role->getId(), $this->roles);
    }

    public function __toString() {
        return $this->name;
    }
}
