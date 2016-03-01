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
        return static::where(compact('username'))->first();
    }

    /**
     * {@inheritdoc}
     */
    public static function findByEmail($email)
    {
        return static::where(compact('email'))->first();
    }

    /**
     * {@inheritdoc}
     */
    public static function findByLogin($login)
    {
        return static::where(['username' => $login])->orWhere(['email' => $login])->first();
    }

    /**
     * {@inheritdoc}
     */
    public static function updateLogin(User $user)
    {
        static::where(['id' => $user->id])->update(['login' => date('Y-m-d H:i:s')]);
    }

    /**
     * Finds user's roles.
     *
     * @param  User $user
     * @return Role[]
     */
    public static function findRoles(User $user)
    {
        static $cached = [];

        if ($ids = array_diff($user->roles, array_keys($cached))) {
            $cached += Role::where('id IN ('.implode(',', $user->roles).')')->get();
        }

        return array_intersect_key($cached, array_flip($user->roles));
    }

    /**
     * @Saving
     */
    public static function saving($event, User $user)
    {
        if (!$user->hasRole(Role::ROLE_AUTHENTICATED)) {
            $user->roles[] = Role::ROLE_AUTHENTICATED;
        }
    }
}
