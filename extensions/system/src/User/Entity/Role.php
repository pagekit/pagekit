<?php

namespace Pagekit\User\Entity;

use Pagekit\User\Model\Role as BaseRole;

/**
 * @Entity(tableClass="@system_role", eventPrefix="system.role")
 */
class Role extends BaseRole
{
    /** @Column(type="integer") @Id */
    protected $id;

    /** @Column(type="string") */
    protected $name;

    /** @Column(type="integer") */
    protected $priority = 0;

    /** @Column(type="simple_array") */
    protected $permissions = array();
}
