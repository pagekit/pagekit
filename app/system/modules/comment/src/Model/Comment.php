<?php

namespace Pagekit\Comment\Model;
use Pagekit\Database\ORM\ModelTrait;

/**
 * @MappedSuperclass
 */
abstract class Comment implements CommentInterface
{
    use ModelTrait;

    /** @Column(type="integer") @Id */
    protected $id;

    /** @Column(type="text") */
    protected $content;

    /** @Column(type="string") */
    protected $author;

    /** @Column(type="datetime") */
    protected $created;

    /** @Column(type="smallint") */
    protected $status = 0;

    /** @Column(type="integer") */
    protected $parent_id;

    /**
     * Should be mapped by the end developer.
     *
     * @var CommentInterface
     */
    protected $parent;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->created = new \DateTime;
    }

    /**
     * {@inheritDoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * {@inheritDoc}
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * {@inheritDoc}
     */
    public function setParent(CommentInterface $parent)
    {
        $this->parent = $parent;
        $this->setParentId($parent->getId());
    }

    /**
     * {@inheritDoc}
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * {@inheritDoc}
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * {@inheritDoc}
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param string $author
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }

    /**
     * {@inheritDoc}
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Sets the creation date
     * @param \DateTime $created
     */
    public function setCreated(\DateTime $created)
    {
        $this->created = $created;
    }

    /**
     * {@inheritDoc}
     */
    public function getStatus()
    {
        return (int) $this->status;
    }

    /**
     * {@inheritDoc}
     */
    public function setStatus($status)
    {
        $this->status = (int) $status;
    }

    /**
     * @return mixed
     */
    public function getParentId()
    {
        return $this->parent_id;
    }

    /**
     * @param mixed $parentId
     */
    public function setParentId($parentId)
    {
        $this->parent_id = $parentId;
    }

    /**
     * @Deleting
     */
    public function Deleting()
    {
        self::where(['parent_id = :old_parent'], [':old_parent' => $this->id])->update(['parent_id' => $this->parent_id]);
    }

    public function __toString()
    {
        return 'Comment #'.$this->getId();
    }
}
