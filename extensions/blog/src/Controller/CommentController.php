<?php

namespace Pagekit\Blog\Controller;

use Pagekit\Blog\Entity\Comment;
use Pagekit\Comment\Event\MarkSpamEvent;
use Pagekit\Comment\Model\CommentInterface;
use Pagekit\Component\Database\ORM\Repository;
use Pagekit\Framework\Controller\Controller;
use Pagekit\Framework\Controller\Exception;

/**
 * @Access("blog: manage comments", admin=true)
 */
class CommentController extends Controller
{
    const COMMENTS_PER_PAGE = 20;

    /**
     * @var Repository
     */
    protected $posts;

    /**
     * @var Repository
     */
    protected $comments;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->posts    = $this('db.em')->getRepository('Pagekit\Blog\Entity\Post');
        $this->comments = $this('db.em')->getRepository('Pagekit\Blog\Entity\Comment');
    }

    /**
     * @Request({"filter": "array", "post":"int", "page":"int"})
     * @View("blog/admin/comment/index.razr")
     */
    public function indexAction($filter = array(), $thread = 0, $page = 0)
    {
        if ($filter) {
            $this('session')->set('blog.comments.filter', $filter);
        } else {
            $filter = $this('session')->get('blog.comments.filter', array());
        }

        $query = $this->comments->query()->related(array('thread'));

        if ($thread) {
            $query->where(array('thread_id = ?'), array($thread));
            $post = $this->posts->find($thread);
        }

        if (isset($filter['status']) && is_numeric($status = $filter['status'])) {
            $query->where(array('status = ?'), array(intval($filter['status'])));
        } else {
            $query->where(function($query) use ($filter) {
                $query->orWhere(array('status = ?', 'status = ?'), array(CommentInterface::STATUS_VISIBLE, CommentInterface::STATUS_PENDING));
            });
        }

        if (isset($filter['search']) && strlen($filter['search'])) {
            $query->where(function($query) use ($filter) {
                $query->orWhere(array('author LIKE :search', 'email LIKE :search', 'url LIKE :search', 'ip LIKE :search', 'content LIKE :search'), array('search' => "%{$filter['search']}%"));
            });
        }

        $limit = self::COMMENTS_PER_PAGE;
        $count = $query->count();
        $total = ceil($count / $limit);
        $page  = max(0, min($total - 1, $page));

        $query->offset($page * $limit)->limit($limit)->orderBy('created', 'DESC');

        if ($this('request')->isXmlHttpRequest()) {
            return $this('response')->json(array(
                'table' => $this('view')->render('view://blog/admin/comment/table.razr', array('count' => $count, 'comments' => $query->get())),
                'total' => $total
            ));
        }

        $title = isset($post) && $post ? __('Comments on %title%', array('%title%' => $post->getTitle())) : __('Comments');

        return array('head.title' => $title, 'comments' => $query->get(), 'statuses' => Comment::getStatuses(), 'filter' => $filter, 'total' => $total, 'count' => $count);
    }

    /**
     * @Request({"comment": "array", "id": "int"})
     * @Token
     */
    public function saveAction($data, $id = 0)
    {
        try {

            $user = $this('user');

            if (!$id || !$comment = $this->comments->find($id)) {

                if (!$parent = $this->comments->find((int) @$data['parent_id'])) {
                    throw new Exception('Invalid comment reply.');
                }

                $comment = new Comment;

                $comment->setUserId((int) $user->getId());
                $comment->setIp($this('request')->getClientIp());
                $comment->setAuthor($user->getName());
                $comment->setEmail($user->getEmail());
                $comment->setUrl($user->getUrl());
                $comment->setStatus(CommentInterface::STATUS_VISIBLE);
                $comment->setThreadId($parent->getThreadId());
                $comment->setParent($parent);
            }

            $this->comments->save($comment, $data);

            $response = array('message' => $id ? __('Comment saved.') : __('Comment created.'));

        } catch (Exception $e) {
            $response = array('message' => $e->getMessage(), 'error' => true);
        }
        return $this('response')->json($response);
    }

    /**
     * @Request({"ids": "int[]"})
     * @Token
     */
    public function deleteAction($ids = array())
    {
        foreach ($ids as $id) {
            if ($comment = $this->comments->find($id)) {
                $this->comments->delete($comment);
            }
        }

        return $this('response')->json(array('message' => _c('{0} No comment deleted.|{1} Comment deleted.|]1,Inf[ Comments deleted.', count($ids))));
    }

    /**
     * @Request({"status": "int", "ids": "int[]"})
     * @Token
     */
    public function statusAction($status, $ids = array())
    {
        foreach ($ids as $id) {
            if ($comment = $this->comments->find($id) and $comment->getStatus() != $status) {
                $previous = $comment->getStatus();
                $comment->setStatus($status);
                $this->comments->save($comment);

                $this('events')->dispatch('system.comment.spam_mark', new MarkSpamEvent($comment, $previous));
            }
        }

        return $this('response')->json(array('message' => _c('{0} No comment status updated.|{1} Comment status updated.|]1,Inf[ Comment statuses updated.', count($ids))));
    }
}