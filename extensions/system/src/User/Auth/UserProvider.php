<?php

namespace Pagekit\User\Auth;

use Pagekit\Component\Auth\Encoder\PasswordEncoderInterface;
use Pagekit\Component\Auth\UserInterface;
use Pagekit\Component\Auth\UserProviderInterface;
use Pagekit\Framework\ApplicationTrait;

class UserProvider implements UserProviderInterface, \ArrayAccess
{
    use ApplicationTrait;

    /**
     * @var PasswordEncoderInterface
     */
    protected $encoder;

    /**
     * Constructor.
     *
     * @param PasswordEncoderInterface $encoder
     */
    public function __construct(PasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return $this['users']->get($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findByUsername($username)
    {
        return $this['users']->getByUsername($username);
    }

    /**
     * {@inheritdoc}
     */
    public function findByCredentials(array $credentials)
    {
        if (isset($credentials['password'])) {
            unset($credentials['password']);
        }

        return $this['users']->getUserRepository()->where($credentials)->related('roles')->first();
    }

    /**
     * {@inheritdoc}
     */
    public function validateCredentials(UserInterface $user, array $credentials)
    {
        return $this->encoder->verify($user->getPassword(), $credentials['password']);
    }
}
