<?php

namespace Pagekit\Blog\Controller;

use Pagekit\Application as App;
use Pagekit\Application\Controller;
use Pagekit\Application\Exception;
use Pagekit\Blog\Entity\Comment;
use Pagekit\Blog\Entity\Post;
use Pagekit\Comment\Event\MarkSpamEvent;

/**
 * @Access("blog: manage comments", admin=true)
 */
class CommentController extends Controller
{
    /**
     * @Request({"filter": "array", "post":"int", "page":"int"})
     * @Response("extensions/blog/views/admin/comment/index.razr")
     */
    public function indexAction($filter = [], $post_id = 0, $page = 0)
    {
        if ($filter) {
            App::session()->set('blog.comments.filter', $filter);
        } else {
            $filter = App::session()->get('blog.comments.filter', []);
        }

        $query = Comment::query()->related(['post']);

        $post  = null;
        if ($post_id) {
            $query->where(['post_id = ?'], [$post_id]);
            $post = Post::find($post_id);
        }

        if (isset($filter['status']) && is_numeric($status = $filter['status'])) {
            $query->where(['status = ?'], [intval($filter['status'])]);
        } else {
            $query->where(function($query) use ($filter) {
                $query->orWhere(['status = ?', 'status = ?'], [Comment::STATUS_APPROVED, Comment::STATUS_PENDING]);
            });
        }

        if (isset($filter['search']) && strlen($filter['search'])) {
            $query->where(function($query) use ($filter) {
                $query->orWhere(['author LIKE :search', 'email LIKE :search', 'url LIKE :search', 'ip LIKE :search', 'content LIKE :search'], ['search' => "%{$filter['search']}%"]);
            });
        }

        $limit    = App::module('blog')->getParams('comments.comments_per_page');
        $count    = $query->count();
        $total    = ceil($count / $limit);
        $page     = max(0, min($total - 1, $page));
        $comments = $query->offset($page * $limit)->limit($limit)->orderBy('created', 'DESC')->get();

        if ($comments) {
            $pending = App::db()->createQueryBuilder()
                ->from('@blog_comment')
                ->where(['status' => Comment::STATUS_PENDING])
                ->whereIn('post_id', array_unique(array_map(function($comment) { return $comment->getPostId(); }, $comments)))
                ->groupBy('post_id')
                ->execute('post_id, count(id)')
                ->fetchAll(\PDO::FETCH_KEY_PAIR);
        } else {
            $pending = [];
        }

        foreach ($comments as $comment) {
            $comment->setContent(App::content()->applyPlugins($comment->getContent(), ['comment' => true]));
        }

        if (App::request()->isXmlHttpRequest()) {
            return App::response()->json([
                'table' => App::view('extensions/blog/views/admin/comment/table.razr', ['count' => $count, 'comments' => $comments, 'post' => $post, 'pending' => $pending]),
                'total' => $total
            ]);
        }

        $title = $post ? __('Comments on %title%', ['%title%' => $post->getTitle()]) : __('Comments');

        return ['head.title' => $title, 'comments' => $comments, 'post' => $post, 'statuses' => Comment::getStatuses(), 'filter' => $filter, 'total' => $total, 'count' => $count, 'pending' => $pending];
    }

    /**
     * @Request({"id": "int"})
     * @Response("extensions/blog/views/admin/comment/edit.razr", layout=false)
     */
    public function editAction($id = 0)
    {
        try {

            if (!$comment = Comment::find($id)) {
                throw new Exception('Invalid comment.');
            }

            return ['comment' => $comment];

        } catch (Exception $e) {}
    }

    /**
     * @Request({"comment": "array", "id": "int"}, csrf=true)
     * @Response("json")
     */
    public function saveAction($data, $id = 0)
    {
        try {

            $user = App::user();

            if (!$id || !$comment = Comment::find($id)) {

                if (!$parent = Comment::find((int) @$data['parent_id'])) {
                    throw new Exception('Invalid comment reply.');
                }

                $comment = new Comment;
                $comment->setUserId((int) $user->getId());
                $comment->setIp(App::request()->getClientIp());
                $comment->setAuthor($user->getName());
                $comment->setEmail($user->getEmail());
                $comment->setUrl($user->getUrl());
                $comment->setStatus(Comment::STATUS_APPROVED);
                $comment->setPostId($parent->getPostId());
                $comment->setParent($parent);
            }

            $comment->save($data);

            return ['message' => $id ? __('Comment saved.') : __('Comment created.')];

        } catch (Exception $e) {

            return ['message' => $e->getMessage(), 'error' => true];
        }
    }

    /**
     * @Request({"ids": "int[]"}, csrf=true)
     * @Response("json")
     */
    public function deleteAction($ids = [])
    {
        foreach ($ids as $id) {
            if ($comment = Comment::find($id)) {
                $comment->delete();
            }
        }

        return ['message' => _c('{0} No comment deleted.|{1} Comment deleted.|]1,Inf[ Comments deleted.', count($ids))];
    }

    /**
     * @Request({"status": "int", "ids": "int[]"}, csrf=true)
     * @Response("json")
     */
    public function statusAction($status, $ids = [])
    {
        foreach ($ids as $id) {
            if ($comment = Comment::find($id) and $comment->getStatus() != $status) {
                $previous = $comment->getStatus();
                $comment->setStatus($status);
                $comment->save();

                App::trigger('system.comment.spam_mark', new MarkSpamEvent($comment, $previous));
            }
        }

        return ['message' => _c('{0} No comment status updated.|{1} Comment status updated.|]1,Inf[ Comment statuses updated.', count($ids))];
    }
}
