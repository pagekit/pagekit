<?php

namespace Pagekit\System\DataCollector;

use Pagekit\Component\Auth\Auth;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

/**
 * UserDataCollector.
 */
class UserDataCollector extends DataCollector
{
    protected $auth;

    /**
     * Constructor.
     *
     * @param Auth $auth
     */
    public function __construct(Auth $auth = null)
    {
        $this->auth = $auth;
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        if (null === $this->auth) {
            $this->data = [
                'enabled'       => false,
                'authenticated' => false,
                'user_class'   => null,
                'user'          => '',
                'roles'         => [],
            ];
        } elseif (null === $user = $this->auth->getUser()) {
            $this->data = [
                'enabled'       => true,
                'authenticated' => false,
                'user_class'   => null,
                'user'          => '',
                'roles'         => [],
            ];
        } else {
            $this->data = [
                'enabled'       => true,
                'authenticated' => $user->isAuthenticated(),
                'user_class'   => get_class($user),
                'user'          => $user->getUsername(),
                'roles'         => array_map(function ($role) { return $role->getName(); }, $user->getRoles()),
            ];
        }
    }

    /**
     * Checks if security is enabled.
     *
     * @return boolean true if security is enabled, false otherwise
     */
    public function isEnabled()
    {
        return $this->data['enabled'];
    }

    /**
     * Gets the user.
     *
     * @return string The user
     */
    public function getUser()
    {
        return $this->data['user'];
    }

    /**
     * Gets the roles of the user.
     *
     * @return array The roles
     */
    public function getRoles()
    {
        return $this->data['roles'];
    }

    /**
     * Checks if the user is authenticated or not.
     *
     * @return boolean true if the user is authenticated, false otherwise
     */
    public function isAuthenticated()
    {
        return $this->data['authenticated'];
    }

    /**
     * Get the class name of the security user.
     *
     * @return string The user
     */
    public function getUserClass()
    {
        return $this->data['user_class'];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'auth';
    }
}
