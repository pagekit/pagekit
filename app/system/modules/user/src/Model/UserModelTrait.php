<?php

namespace Pagekit\User\Model;

use Pagekit\Database\ORM\ModelTrait;

trait UserModelTrait
{
    use ModelTrait;

    /**
     * {@inheritdoc}
     */
    public static function find($id)
    {
        return self::where(compact('id'))->related('roles')->first();
    }

    /**
     * {@inheritdoc}
     */
    public static function findAll()
    {
        return self::query()->related('roles')->get();
    }

    /**
     * {@inheritdoc}
     */
    public static function findByUsername($username)
    {
        return self::where(compact('username'))->related('roles')->first();
    }

    /**
     * {@inheritdoc}
     */
    public static function findByEmail($email)
    {
        return self::where(compact('email'))->related('roles')->first();
    }

    /**
     * {@inheritdoc}
     */
    public static function findByLogin($login)
    {
        return self::where(['username' => $login])->orWhere(['email' => $login])->first();
    }

    /**
     * {@inheritdoc}
     */
    public static function updateLogin(UserInterface $user)
    {
        self::where(['id' => $user->getId()])->update(['login' => date('Y-m-d H:i:s')]);
    }

    /**
     * {@inheritdoc}
     */
    public static function updateAccess(UserInterface $user)
    {
        self::where(['id' => $user->getId()])->update(['access' => date('Y-m-d H:i:s')]);
    }
}
