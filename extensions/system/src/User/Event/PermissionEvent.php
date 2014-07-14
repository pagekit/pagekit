<?php

namespace Pagekit\User\Event;

use Pagekit\Framework\Event\Event;

class PermissionEvent extends Event
{
    /**
     * @var array
     */
    protected $permissions = [];

    /**
     * Gets the permissions.
     *
     * @return array
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * Set permissions for an extension.
     *
     * @param string $extension
     * @param array  $permissions
     */
    public function setPermissions($extension, array $permissions)
    {
        $this->permissions[$extension] = $permissions;
    }
}
