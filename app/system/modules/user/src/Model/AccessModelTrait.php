<?php

namespace Pagekit\User\Model;

use Pagekit\Application as App;

trait AccessModelTrait
{
    /** @Column(type="simple_array") */
    public $roles = [];

    /**
     * @param  int $role
     * @return bool
     */
    public function hasRole($role)
    {
        return in_array($role, $this->roles);
    }

    /**
     * @param  User $user
     * @return bool
     */
    public function hasAccess(User $user)
    {
        return !$this->roles or array_intersect($user->roles, $this->roles);
    }

    /**
     * @param  Role|int $role
     * @return int
     */
    public static function removeRole($role)
    {
        if ($role instanceof Role) {
            $role = $role->id;
        }

        $db = self::getConnection();
        $platform = $db->getDatabasePlatform();

        return $db->executeUpdate('UPDATE '.self::getMetadata()->getTable().' SET roles = NULLIF('.$platform->getTrimExpression("REPLACE (".$platform->getConcatExpression($db->quote(','), 'roles', $db->quote(',')).", ',{$role},', ',')", 3, $db->quote(',')). ", '')");
    }
}
