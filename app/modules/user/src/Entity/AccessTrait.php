<?php

namespace Pagekit\User\Entity;

use Pagekit\User\Model\RoleInterface;
use Pagekit\User\Model\UserInterface;

trait AccessTrait
{
    /** @Column(type="simple_array") */
    protected $roles = [];

    /**
     * @return int[]
     */
    public function getRoles()
    {
        return (array) $this->roles;
    }

    /**
     * @param $roles int[]
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;
    }

    /**
     * @param  RoleInterface $role
     * @return bool
     */
    public function hasRole(RoleInterface $role)
    {
        return in_array($role->getId(), $this->getRoles());
    }

    /**
     * @param  UserInterface $user
     * @return bool
     */
    public function hasAccess(UserInterface $user)
    {
        return !$roles = $this->getRoles() or array_intersect(array_keys($user->getRoles()), $roles);
    }
}
