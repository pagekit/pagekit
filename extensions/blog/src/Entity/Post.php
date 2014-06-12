<?php

namespace Pagekit\Blog\Entity;

use Pagekit\Comment\Entity\Thread;
use Pagekit\Framework\Database\Event\EntityEvent;
use Pagekit\User\Model\RoleInterface;
use Pagekit\User\Model\UserInterface;

/**
 * @Entity(repositoryClass="Pagekit\Blog\Entity\PostRepository", tableClass="@blog_post", eventPrefix="blog.post")
 */
class Post extends Thread
{
    /* Post draft status. */
    const STATUS_DRAFT = 0;

    /* Post pending review status. */
    const STATUS_PENDING_REVIEW = 1;

    /* Post published. */
    const STATUS_PUBLISHED = 2;

    /* Post unpublished. */
    const STATUS_UNPUBLISHED = 3;

    /** @Column(type="integer") @Id */
    protected $id;

    /** @Column(type="string") */
    protected $title;

    /** @Column(type="string") */
    protected $slug;

    /** @Column(type="integer") */
    protected $user_id;

    /** @Column(type="datetime")*/
    protected $date;

    /** @Column(type="string") */
    protected $subtitle;

    /** @Column(type="text") */
    protected $content;

    /** @Column(type="text") */
    protected $excerpt;

    /** @Column(type="smallint") */
    protected $status;

    /** @Column(type="datetime") */
    protected $modified;

    /** @Column(type="simple_array") */
    protected $roles = array();

    /** @Column(type="json_array") */
    protected $data;

    /**
     * @BelongsTo(targetEntity="Pagekit\User\Entity\User", keyFrom="user_id")
     */
    protected $user;

    /**
     * @HasMany(targetEntity="Comment", keyFrom="id", keyTo="thread_id")
     * @OrderBy({"created" = "DESC"})
     */
    protected $comments;

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    public function getUserId()
    {
        return $this->user_id;
    }

    public function setUserId($userId)
    {
        $this->user_id = $userId;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setDate(\DateTime $date)
    {
        $this->date = $date;
    }

    public function getSubtitle()
    {
        return $this->subtitle;
    }

    public function setSubtitle($subtitle)
    {
        $this->subtitle = $subtitle;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function getExcerpt()
    {
        return $this->excerpt;
    }

    public function setExcerpt($excerpt)
    {
        $this->excerpt = $excerpt;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getModified()
    {
        return $this->modified;
    }

    public function setModified(\DateTime $modified)
    {
        $this->modified = $modified;
    }


    public function hasAccess(UserInterface $user)
    {
        return !$roles = $this->getRoles() or array_intersect(array_keys($user->getRoles()), $roles);
    }

    /**
     * @param  RoleInterface $role
     * @return bool
     */
    public function hasRole(RoleInterface $role)
    {
        return in_array($role->getId(), $this->getRoles());
    }

    /**
     * @return int[]
     */
    public function getRoles()
    {
        return (array) $this->roles;
    }

    /**
     * @param $roles int[]
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    public function get($key, $default = null)
    {
        return isset($this->data[$key]) ? $this->data[$key] : $default;
    }

    public function set($key, $value)
    {
        $this->data[$key] = $value;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user)
    {
        $this->user = $user;
    }

    public static function getStatuses()
    {
        return array(
            self::STATUS_DRAFT          => __('Draft'),
            self::STATUS_PENDING_REVIEW => __('Pending Review'),
            self::STATUS_PUBLISHED      => __('Published'),
            self::STATUS_UNPUBLISHED    => __('Unpublished')
        );
    }

    public function getStatusText()
    {
        $statuses = self::getStatuses();

        return isset($statuses[$this->status]) ? $statuses[$this->status] : __('Unknown');
    }

    /**
     * @PreSave
     */
    public function preSave(EntityEvent $event)
    {
        $this->modified = new \DateTime;

        $repository = $event->getEntityManager()->getRepository(get_class($this));

        $i = 2;
        $id = $this->id;
        while ($repository->query()->where('slug = ?', array($this->slug))->where(function($query) use($id) { if ($id) $query->where('id <> ?', array($id)); })->first()) {
            $this->slug = preg_replace('/-\d+$/', '', $this->slug).'-'.$i++;
        }
    }

    /**
     * @PreDelete
     */
    public function preDelete(EntityEvent $event)
    {
        $event->getConnection()->delete('@blog_comment', array('thread_id' => $this->getId()));
    }

    /**
     * @param  int $autoclose The number of days after which comments are closed automatically
     * @return boolean
     */
    public function isCommentable($autoclose = 0)
    {
        if ($autoclose) {
            if ($this->getDate() < new \DateTime("-{$autoclose} day")) {
                return false;
            }
        }

        return $this->is_commentable;
    }
}
