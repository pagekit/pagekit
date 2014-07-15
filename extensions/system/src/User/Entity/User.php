<?php

namespace Pagekit\User\Entity;

use Pagekit\Framework\Database\Event\EntityEvent;
use Pagekit\System\Entity\DataTrait;
use Pagekit\User\Model\User as BaseUser;

/**
 * @Entity(repositoryClass="Pagekit\User\Entity\UserRepository", tableClass="@system_user", eventPrefix="system.user")
 */
class User extends BaseUser
{
    use DataTrait;

    /** @Column(type="integer") @Id */
    protected $id;

    /** @Column */
    protected $username = '';

    /** @Column */
    protected $password = '';

    /** @Column */
    protected $email = '';

    /** @Column */
    protected $url = '';

    /** @Column(type="datetime") */
    protected $registered;

    /** @Column(type="integer") */
    protected $status = 0;

    /** @Column */
    protected $name;

    /** @Column(type="datetime") */
    protected $access;

    /** @Column(type="datetime") */
    protected $login;

    /** @Column */
    protected $activation;

    /** @Column(type="json_array") */
    protected $data;

    /** @ManyToMany(targetEntity="Role", keyFrom="id", keyTo="id", tableThrough="@system_user_role", keyThroughFrom="user_id", keyThroughTo="role_id") */
    protected $roles;

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getStatusText()
    {
        $statuses = self::getStatuses();

        return isset($statuses[$this->status]) ? $statuses[$this->status] : __('Unknown');
    }

    public static function getStatuses()
    {
        return [
            self::STATUS_ACTIVE  => __('Active'),
            self::STATUS_BLOCKED => __('Blocked')
        ];
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getRegistered()
    {
        return $this->registered;
    }

    public function setRegistered(\DateTime $registered)
    {
        $this->registered = $registered;
    }

    public function getLogin()
    {
        return $this->login;
    }

    public function setLogin(\DateTime $login)
    {
        $this->login = $login;
    }

    public function getAccess()
    {
        return $this->access;
    }

    public function setAccess(\DateTime $access)
    {
        $this->access = $access;
    }

    public function getActivation()
    {
        return $this->activation;
    }

    public function setActivation($activation)
    {
        $this->activation = $activation;
    }

    public function hasAccess($expression)
    {
        return $this->isAdministrator() || parent::hasAccess($expression);
    }

    public function isNew()
    {
        return $this->isBlocked() && !$this->access;
    }

    /**
     * Save related user roles.
     *
     * @PostSave
     */
    public function postSave(EntityEvent $event)
    {
        if (is_array($this->roles)) {

            $connection = $event->getConnection();
            $connection->delete('@system_user_role', ['user_id' => $this->getId()]);

            if (!array_key_exists(Role::ROLE_AUTHENTICATED, $this->roles)) {
                $this->roles[Role::ROLE_AUTHENTICATED] = $event->getEntityManager()->find('Pagekit\User\Entity\Role', Role::ROLE_AUTHENTICATED);
            }

            foreach ($this->roles as $role) {
                $connection->insert('@system_user_role', ['user_id' => $this->getId(), 'role_id' => $role->getId()]);
            }
        }
    }

    /**
     * Delete all orphaned user role relations.
     *
     * @PostDelete
     */
    public function postDelete(EntityEvent $event)
    {
        $event->getConnection()->delete('@system_user_role', ['user_id' => $this->getId()]);
    }
}
