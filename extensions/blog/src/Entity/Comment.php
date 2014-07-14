<?php

namespace Pagekit\Blog\Entity;

use Pagekit\Comment\Entity\Comment as BaseComment;

/**
 * @Entity(tableClass="@blog_comment", eventPrefix="blog.comment")
 */
class Comment extends BaseComment
{
    /** @Column(type="integer") */
    protected $post_id;

    /** @Column(type="string") */
    protected $user_id;

    /** @Column(type="string") */
    protected $email;

    /** @Column(type="string") */
    protected $url = '';

    /** @Column(type="string") */
    protected $ip;

    /** @BelongsTo(targetEntity="Post", keyFrom="post_id") */
    protected $post;

    /** @BelongsTo(targetEntity="Pagekit\User\Entity\User", keyFrom="user_id") */
    protected $user;

    public function getPostId()
    {
        return $this->post_id;
    }

    public function setPostId($postId)
    {
        $this->post_id = $postId;
    }

    public function getUserId()
    {
        return $this->user_id;
    }

    public function setUserId($userId)
    {
        $this->user_id = $userId;
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

    public function getIp()
    {
        return $this->ip;
    }

    public function setIp($ip)
    {
        $this->ip = $ip;
    }

    public function getPost()
    {
        return $this->post;
    }

    public function setPost($post)
    {
        $this->post = $post;
        $this->post_id = $post->getId();
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user)
    {
        $this->user = $user;
    }

    public function getStatusText()
    {
        $statuses = self::getStatuses();

        return isset($statuses[$this->status]) ? $statuses[$this->status] : __('Unknown');
    }

    public static function getStatuses()
    {
        return [
            self::STATUS_APPROVED => __('Approved'),
            self::STATUS_PENDING  => __('Pending'),
            self::STATUS_SPAM     => __('Spam')
        ];
    }
}
