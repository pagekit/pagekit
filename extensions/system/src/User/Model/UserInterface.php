<?php

namespace Pagekit\User\Model;

use Pagekit\Component\Auth\UserInterface as AuthUserInterface;

interface UserInterface extends AuthUserInterface
{
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

    /**
     * Retrieves the identifier
     *
     * @return string Identifier
     */
    public function getId();

    /**
     * Retrieves the username
     *
     * @return string Username
     */
    public function getUsername();

    /**
     * Retrieves the password
     *
     * @return string Password
     */
    public function getPassword();

    public function getAccess();

    public function getLogin();

    public function getActivation();

    public function getEmail();

    public function getUrl();

    public function getRegistered();

    public function getStatus();

    public function getName();

    public function getRoles();

    public function hasAccess($expression);

    public function isAuthenticated();

    public static function getStatuses();

	public function getStatusText();
}
