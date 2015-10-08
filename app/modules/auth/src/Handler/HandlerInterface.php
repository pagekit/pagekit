<?php

namespace Pagekit\Auth\Handler;

interface HandlerInterface
{
    /**
     * Gets the current user.
     *
     * @return int|null
     */
    public function read();

    /**
     * Sets the current user.
     *
     * @param  int  $user
     * @param  bool $remember
     */
    public function write($user, $remember = false);

    /**
     * Removes the user.
     *
     * @param UserInterface
     */
    public function destroy();
}
