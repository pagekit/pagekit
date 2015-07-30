<?php

namespace Pagekit\Blog\Model;

use Pagekit\Comment\Model\Comment as BaseComment;

/**
 * @Entity(tableClass="@blog_comment")
 */
class Comment extends BaseComment implements \JsonSerializable
{
    /** @Column(type="integer") */
    public $post_id;

    /** @Column(type="string") */
    public $user_id;

    /** @Column(type="string") */
    public $email;

    /** @Column(type="string") */
    public $url = '';

    /** @Column(type="string") */
    public $ip;

    /** @BelongsTo(targetEntity="Post", keyFrom="post_id") */
    public $post;

    /** @BelongsTo(targetEntity="Pagekit\User\Model\User", keyFrom="user_id") */
    public $user;

    public function setPost($post)
    {
        $this->post = $post;

        if ($post) {
            $this->post_id = $post->id;
        }
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
