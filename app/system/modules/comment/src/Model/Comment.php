<?php

namespace Pagekit\Comment\Model;

/**
 * @MappedSuperclass
 */
abstract class Comment
{
    use CommentModelTrait;

    const STATUS_PENDING = 0;
    const STATUS_APPROVED = 1;
    const STATUS_SPAM = 2;

    /** @Column(type="integer") @Id */
    public $id;

    /** @Column(type="text") */
    public $content;

    /** @Column(type="string") */
    public $author;

    /** @Column(type="datetime") */
    public $created;

    /** @Column(type="smallint") */
    public $status = 0;

    /** @Column(type="integer") */
    public $parent_id;

    /**
     * Should be mapped by the end developer.
     *
     * @var \Pagekit\Comment\Model\Comment
     */
    public $parent;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->created = new \DateTime;
    }

    public function __toString()
    {
        return 'Comment #'.$this->id;
    }
}
