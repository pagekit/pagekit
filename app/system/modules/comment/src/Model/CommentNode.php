<?php

namespace Pagekit\Comment\Model;

use Pagekit\Tree\Node;

class CommentNode extends Node
{
    /**
     * @var Comment
     */
    protected $comment;

    /**
     * @param Comment $item
     */
    public function setComment(Comment $item)
    {
        $this->comment = $item;
    }

    /**
     * @return Comment
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->comment;
    }

    /**
     * @param  string $method
     * @param  array  $args
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function __call($method, $args)
    {
        if (!$this->comment) {
            return;
        }

        if (!is_callable($callable = [$this->comment, $method])) {
            throw new \InvalidArgumentException(sprintf('Undefined method call "%s::%s"', get_class($this->comment), $method));
        }

        return call_user_func_array($callable, $args);
    }
}
