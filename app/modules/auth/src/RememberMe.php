<?php

namespace Pagekit\Auth;

use Pagekit\Auth\Exception\AuthException;
use Pagekit\Cookie\CookieJar;
use Symfony\Component\HttpFoundation\Request;

class RememberMe
{
    const COOKIE_DELIMITER = ':';
    const COOKIE_LIFETIME = 1209600;
    const REMEMBER_ME_PARAM = '_remember_me';

    /**
     * The key used for hashing
     *
     * @var string
     */
    protected $key;

    /**
     * The cookie name
     *
     * @var string
     */
    protected $name;

    /**
     * @var CookieJar
     */
    protected $cookie;

    /**
     * Constructor
     *
     * @param string $key
     * @param string $name
     * @param CookieJar $cookie
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($key, $name, CookieJar $cookie)
    {
        $this->key    = $key;
        $this->name   = $name;
        $this->cookie = $cookie;
    }

    /**
     * Tries to read the username from the cookie.
     *
     * @param  UserProviderInterface $provider
     * @return UserInterface
     * @throws AuthException
     */
    public function autoLogin(UserProviderInterface $provider)
    {
        try {

            if (null === $cookie = $this->cookie->get($this->name)) {
                throw new AuthException('No remember me cookie found.');
            }

            $cookieParts = $this->decodeCookie($cookie);

            if (count($cookieParts) !== 3) {
                throw new AuthException('The cookie is invalid.');
            }

            list($username, $expires, $hash) = $cookieParts;

            if ($expires < time()) {
                throw new AuthException('The cookie has expired.');
            }

            if (false === $username = base64_decode($username, true)) {
                throw new AuthException(sprintf('"%s" contains a character from outside the base64 alphabet.', $username));
            }

            if (!$user = $provider->findByUsername($username)) {
                throw new AuthException(sprintf('No user found for "%s".', $username));
            }

            if (true !== $this->compareHashes($hash, $this->generateCookieHash($username, $expires, $user->getPassword()))) {
                throw new AuthException('The cookie\'s hash is invalid.');
            }

        } catch (AuthException $e) {

            $this->remove();

            throw $e;
        }

        return $user;
    }

    /**
     * This is called when an authentication is successful.
     *
     * @param Request $request
     * @param UserInterface $user
     */
    public function set(Request $request, UserInterface $user)
    {
        $this->remove();

        if (!$this->isRememberMeRequested($request)) {
            return;
        }


        $expires = self::COOKIE_LIFETIME + time();
        $value = $this->generateCookieValue($user->getUsername(), $expires, $user->getPassword());

        $this->cookie->set($this->name, $value, $expires);
    }

    /**
     * Deletes the remember-me cookie
     */
    public function remove()
    {
        if ($this->cookie->has($this->name)) {
            $this->cookie->remove($this->name);
        }
    }

    /**
     * Decodes the raw cookie value
     *
     * @param string $rawCookie
     *
     * @return array
     */
    protected function decodeCookie($rawCookie)
    {
        return explode(self::COOKIE_DELIMITER, base64_decode($rawCookie));
    }

    /**
     * Encodes the cookie parts
     *
     * @param array $cookieParts
     *
     * @return string
     */
    protected function encodeCookie(array $cookieParts)
    {
        return base64_encode(implode(self::COOKIE_DELIMITER, $cookieParts));
    }

    /**
     * Checks whether remember-me capabilities where requested
     *
     * @param Request $request
     * @return Boolean
     */
    protected function isRememberMeRequested(Request $request)
    {
        $parameter = $request->get(self::REMEMBER_ME_PARAM, null, true);

        return $parameter === 'true' || $parameter === 'on' || $parameter === '1' || $parameter === 'yes';
    }

    /**
     * Generates the cookie value.
     *
     * @param string  $username The username
     * @param integer $expires The unixtime when the cookie expires
     * @param string  $password The encoded password
     * @return string
     */
    protected function generateCookieValue($username, $expires, $password)
    {
        return $this->encodeCookie([base64_encode($username), $expires, $this->generateCookieHash($username, $expires, $password)]);
    }

    /**
     * Generates a hash for the cookie to ensure it is not being tempered with
     *
     * @param string  $username The username
     * @param integer $expires  The unixtime when the cookie expires
     * @param string  $password The encoded password
     *
     * @return string
     */
    protected function generateCookieHash($username, $expires, $password)
    {
        return sha1($username . $expires . $password . $this->key);
    }

    /**
     * Compares two hashes using a constant-time algorithm to avoid (remote)
     * timing attacks.
     *
     * @param string $hash1 The first hash
     * @param string $hash2 The second hash
     *
     * @return Boolean true if the two hashes are the same, false otherwise
     */
    private function compareHashes($hash1, $hash2)
    {
        if (strlen($hash1) !== $c = strlen($hash2)) {
            return false;
        }

        $result = 0;
        for ($i = 0; $i < $c; $i++) {
            $result |= ord($hash1[$i]) ^ ord($hash2[$i]);
        }

        return 0 === $result;
    }
}
