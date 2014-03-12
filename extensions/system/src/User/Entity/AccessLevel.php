<?php

namespace Pagekit\User\Entity;

use Pagekit\User\Model\AccessLevel as BaseAccessLevel;

/**
 * @Entity(tableClass="@system_access_level", eventPrefix="system.accesslevel")
 */
class AccessLevel extends BaseAccessLevel
{
    /** @Column(type="integer") @Id */
    protected $id;

    /** @Column(type="string") */
    protected $name;

    /** @Column(type="integer") */
    protected $priority = 0;

    /** @Column(type="simple_array") */
    protected $roles = array();

    /**
     * {@inheritdoc}
     */
    public function isLocked()
    {
        return in_array($this->id, array(self::LEVEL_PUBLIC, self::LEVEL_REGISTERED));
    }

    /**
     * @PreSave
     */
    public function preSave($manager)
    {
        if (!$this->id) {
            $this->setPriority($manager->getConnection()->fetchColumn('SELECT MAX(priority) + 1 FROM @system_access_level'));
        }
    }
}
