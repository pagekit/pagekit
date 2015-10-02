<?php

namespace Pagekit\Auth\Handler;

interface HandlerInterface
{
    /**
     * Gets the current user.
     *
     * @return int|null
     */
    public function find();

    /**
     * Sets the current user.
     *
     * @param  int $user
     * @param  bool          $remember
     */
    public function set($user, $remember = false);

    /**
     * Removes the user.
     *
     * @param UserInterface
     */
    public function remove();
}
