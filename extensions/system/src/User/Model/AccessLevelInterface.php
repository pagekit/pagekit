<?php

namespace Pagekit\User\Model;

interface AccessLevelInterface
{
    /**
     * The identifier of the public access level.
     *
     * @var int
     */
    const LEVEL_PUBLIC = 1;

    /**
     * The identifier of the registered access level.
     *
     * @var int
     */
    const LEVEL_REGISTERED = 2;

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
