<?php

namespace Pagekit\Session\Csrf\Provider;

interface CsrfProviderInterface
{
    /**
     * Generates a CSRF token.
     */
    public function generate();

    /**
     * Validates a CSRF token.
     *
     * @param  string $token
     * @return bool
     */
    public function validate($token = null);

    /**
     * Sets a CSRF token to validate.
     *
     * @param string $token
     */
    public function setToken($token);
}
