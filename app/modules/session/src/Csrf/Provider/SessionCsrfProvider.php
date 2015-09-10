<?php

namespace Pagekit\Session\Csrf\Provider;

use Symfony\Component\HttpFoundation\Session\Session;

class SessionCsrfProvider extends DefaultCsrfProvider
{
    /**
     * The session.
     *
     * @var Session
     */
    protected $session;

    /**
     * Constructor.
     *
     * @param Session $session
     * @param string  $name
     */
    public function __construct(Session $session, $name = '_csrf')
    {
        parent::__construct($name);

        $this->session = $session;
    }

    /**
     * {@inheritdoc}
     */
    protected function getSessionId()
    {
        if (!$this->session->isStarted()) {
            $this->session->start();
        }

        return $this->session->getId();
    }

    /**
     * {@inheritdoc}
     */
    protected function getSessionToken()
    {
        if (!$this->session->has($this->name)) {
            $this->session->set($this->name, sha1(uniqid(rand(), true)));
        }

        return $this->session->get($this->name);
    }
}
