<?php

namespace Pagekit\User\Model;

use Pagekit\Database\ORM\ModelTrait;

trait UserModelTrait
{
    use ModelTrait;

    /**
     * {@inheritdoc}
     */
    public static function findByUsername($username)
    {
        return self::where(compact('username'))->first();
    }

    /**
     * {@inheritdoc}
     */
    public static function findByEmail($email)
    {
        return self::where(compact('email'))->first();
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

    /**
     * Finds user's roles.
     *
     * @param  UserInterface $user
     * @return RoleInterface[]
     */
    public static function findRoles(UserInterface $user)
    {
        static $cached = [];

        $roles = $user->getRoles();

        if ($ids = array_diff($roles, array_keys($cached))) {
            $cached += Role::where('id IN ('.implode(',', $user->getRoles()).')')->get();
        }

        return array_intersect_key($cached, array_flip($roles));
    }
}
