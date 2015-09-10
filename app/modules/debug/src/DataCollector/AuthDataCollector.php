<?php

namespace Pagekit\Debug\DataCollector;

use DebugBar\DataCollector\DataCollectorInterface;
use Pagekit\Auth\Auth;
use Pagekit\User\Model\User;

class AuthDataCollector implements DataCollectorInterface
{
    /**
     * @var Auth
     */
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
    public function collect()
    {
        if (null === $this->auth) {
            return [
                'enabled' => false,
                'authenticated' => false,
                'user_class' => null,
                'user' => '',
                'roles' => [],
            ];
        }

        try {
            $user = $this->auth->getUser();
        } catch (\Exception $e) {
            $user = null;
        }

        if (null === $user) {
            return [
                'enabled' => true,
                'authenticated' => false,
                'user_class' => null,
                'user' => '',
                'roles' => [],
            ];
        }

        return [
            'enabled' => true,
            'authenticated' => $user->isAuthenticated(),
            'user_class' => get_class($user),
            'user' => $user->getUsername(),
            'roles' => array_map(function ($role) {
                return $role->name;
            }, User::findRoles($user)), // TODO interface does not match
        ];

    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'auth';
    }
}
