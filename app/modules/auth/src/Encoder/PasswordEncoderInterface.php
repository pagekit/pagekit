<?php

namespace Pagekit\Auth\Encoder;

interface PasswordEncoderInterface
{
    /**
     * Encodes the raw password.
     *
     * @param  string $raw  The password to hash
     * @param  string $salt The salt
     * @return string
     */
    public function hash($raw, $salt = null);

    /**
     * Checks a raw password against an encoded password.
     *
     * @param  string $hash A hashed password
     * @param  string $raw  A raw password
     * @param  string $salt
     * @return boolean
     */
    public function verify($hash, $raw, $salt = null);
}
