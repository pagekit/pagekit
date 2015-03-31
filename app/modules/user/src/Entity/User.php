<?php

namespace Pagekit\User\Entity;

use Pagekit\Database\ORM\ModelTrait;
use Pagekit\System\Entity\DataTrait;
use Pagekit\User\Model\User as BaseUser;
use Pagekit\User\Model\UserInterface;

/**
 * @Entity(tableClass="@system_user", eventPrefix="user")
 */
class User extends BaseUser implements \JsonSerializable
{
    use DataTrait, ModelTrait;

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

    public function jsonSerialize()
    {
        $user = array_diff_key(get_object_vars($this), array_flip(['password', 'activation']));

        foreach (['access', 'login', 'registered'] as $date) {

            $user[$date] = $user[$date] ? $user[$date]->format(\DateTime::ISO8601) : null;

        }

        $user['isNew'] = $this->isNew();
        $user['statusText'] = $this->isNew() ? __('New') : $this->getStatusText();

        return $user;
    }

    /**
     * Save related user roles.
     *
     * @PostSave
     */
    public function postSave()
    {
        if (is_array($this->roles)) {

            self::getConnection()->delete('@system_user_role', ['user_id' => $this->getId()]);

            if (!array_key_exists(Role::ROLE_AUTHENTICATED, $this->roles)) {
                $this->roles[Role::ROLE_AUTHENTICATED] = self::getManager()->find('Pagekit\User\Entity\Role', Role::ROLE_AUTHENTICATED);
            }

            foreach ($this->roles as $role) {
                self::getConnection()->insert('@system_user_role', ['user_id' => $this->getId(), 'role_id' => $role->getId()]);
            }
        }
    }

    /**
     * Delete all orphaned user role relations.
     *
     * @PostDelete
     */
    public function postDelete()
    {
        self::getConnection()->delete('@system_user_role', ['user_id' => $this->getId()]);
    }

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
