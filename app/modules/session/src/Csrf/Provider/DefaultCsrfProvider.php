<?php

namespace Pagekit\Session\Csrf\Provider;

class DefaultCsrfProvider implements CsrfProviderInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $token;

    /**
     * Constructor.
     *
     * @param string $name
     */
    public function __construct($name = '_csrf')
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        return sha1($this->getSessionId().$this->getSessionToken());
    }

    /**
     * {@inheritdoc}
     */
    public function validate($token = null)
    {
        if ($token === null) {
            $token = $this->token;
        }

        return $token === $this->generate();
    }

    /**
     * {@inheritdoc}
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * Returns the session id.
     *
     * @return string
     */
    protected function getSessionId()
    {
        if (!session_id()) {
            session_start();
        }

        return session_id();
    }

    /**
     * Returns the session token.
     *
     * @return string
     */
    protected function getSessionToken()
    {
        if (!isset($_SESSION[$this->name])) {
            $_SESSION[$this->name] = sha1(uniqid(rand(), true));
        }

        return $_SESSION[$this->name];
    }
}
