<?php

namespace Pagekit\Auth\Encoder;

interface PasswordEncoderInterface
{
    /**
     * Encodes the raw password.
     *
     * @param  string $raw  The password to hash
     * @return string
     */
    public function hash($raw);

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
