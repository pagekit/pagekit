<?php

namespace Pagekit\Auth;

interface UserInterface
{
    /**
     * Retrieves the unique identifier
     *
     * @return string Id
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
}
