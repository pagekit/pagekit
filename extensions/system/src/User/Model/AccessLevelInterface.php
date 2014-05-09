<?php

namespace Pagekit\User\Model;

interface AccessLevelInterface
{
    /**
     * The identifier of the everyone access level.
     *
     * @var int
     */
    const LEVEL_EVERYONE = 1;

    /**
     * The identifier of the anonymous access level.
     *
     * @var int
     */
    const LEVEL_ANONYMOUS = 2;

    /**
     * The identifier of the authenticated access level.
     *
     * @var int
     */
    const LEVEL_AUTHENTICATED = 3;

	/**
	 * Returns the access level's ID.
	 *
	 * @return mixed
	 */
	public function getId();

	/**
	 * Returns the access level's name.
	 *
	 * @return string
	 */
	public function getName();

	/**
	 * Returns roles for the access level.
	 *
	 * @return array
	 */
	public function getRoles();

	/**
	 * Returns the access level's priority.
	 *
	 * @return int
	 */
    public function getPriority();
}
