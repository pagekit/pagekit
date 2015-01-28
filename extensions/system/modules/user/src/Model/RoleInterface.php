<?php

namespace Pagekit\User\Model;

interface RoleInterface
{
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

	/**
	 * Returns the role's ID.
	 *
	 * @return mixed
	 */
	public function getId();

	/**
	 * Returns the role's name.
	 *
	 * @return string
	 */
	public function getName();

	/**
	 * Returns permissions for the role.
	 *
	 * @return string[]
	 */
	public function getPermissions();

	/**
	 * Returns the role's priority.
	 *
	 * @return int
	 */
    public function getPriority();

    /**
     * Checks if a permission exists for this role.
     *
     * @param  string $permission
     * @return bool
     */
    public function hasPermission($permission);

    /**
     * Adds a permission
     *
     * @param string $permission
     */
    public function addPermission($permission);
}
