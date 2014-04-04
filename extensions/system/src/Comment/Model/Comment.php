<?php

namespace Pagekit\Comment\Model;

abstract class Comment implements CommentInterface
{
    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var string
     */
    protected $content;

    /**
     * @var string
     */
    protected $author;

    /**
     * @var integer
     */
    protected $depth = 0;

    /**
     * @var \DateTime
     */
    protected $created;

    /**
     * @var integer
     */
    protected $status = 0;

    /**
     * Should be mapped by the end developer.
     *
     * @var CommentInterface
     */
    protected $parent;

    /**
     * Should be mapped by the end developer.
     *
     * @var ThreadInterface
     */
    protected $thread;

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
     * @return int
     */
    public function getDepth()
    {
        return $this->depth;
    }

    /**
     * @param int $depth
     */
    public function setDepth($depth)
    {
        $this->depth = $depth;
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
    public function getThread()
    {
        return $this->thread;
    }

    /**
     * {@inheritDoc}
     */
    public function setThread(ThreadInterface $thread)
    {
        $this->thread = $thread;
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
    }

    public function __toString()
    {
        return 'Comment #'.$this->getId();
    }
}
