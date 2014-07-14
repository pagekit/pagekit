<?php

namespace Pagekit\User\Entity;

use Pagekit\Component\Database\ORM\Repository;
use Pagekit\User\Model\UserInterface;

class UserRepository extends Repository
{
    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return $this->where(compact('id'))->related('roles')->first();
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        return $this->query()->related('roles')->get();
    }

    /**
     * {@inheritdoc}
     */
    public function findByUsername($username)
    {
        return $this->where(compact('username'))->related('roles')->first();
    }

    /**
     * {@inheritdoc}
     */
    public function findByEmail($email)
    {
        return $this->where(compact('email'))->related('roles')->first();
    }

    /**
     * {@inheritdoc}
     */
    public function findByLogin($login)
    {
        return $this->where(['username' => $login])->orWhere(['email' => $login])->first();
    }

    /**
     * {@inheritdoc}
     */
    public function updateLogin(UserInterface $user)
    {
        $this->where(['id' => $user->getId()])->update(['login' => date('Y-m-d H:i:s')]);
    }

    /**
     * {@inheritdoc}
     */
    public function updateAccess(UserInterface $user)
    {
        $this->where(['id' => $user->getId()])->update(['access' => date('Y-m-d H:i:s')]);
    }
}
