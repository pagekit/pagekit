<?php

namespace Pagekit\Blog\Controller;

use Pagekit\Application as App;
use Pagekit\Blog\Entity\Comment;

/**
 * @Access("blog: manage comments")
 * @Route("comment", name="comment")
 */
class CommentApiController
{
    /**
     * @Route("/", methods="GET")
     * @Request({"filter": "array", "post":"int", "page":"int"})
     */
    public function indexAction($filter = [], $post = 0, $page = 0)
    {
        $query  = Comment::query()->related(['post']);
        $filter = array_merge(array_fill_keys(['status', 'search'], ''), $filter);
        extract($filter, EXTR_SKIP);

        if ($post) {
            $query->where(['post_id = ?'], [$post]);
        }

        if (is_numeric($status)) {
            $query->where(['status = ?'], [(int) $status]);
        } else {
            $query->where(function($query) {
                $query->orWhere(['status = ?', 'status = ?'], [Comment::STATUS_APPROVED, Comment::STATUS_PENDING]);
            });
        }

        if ($search) {
            $query->where(function($query) use ($search) {
                $query->orWhere(['author LIKE ?', 'email LIKE ?', 'url LIKE ?', 'ip LIKE ?', 'content LIKE ?'], array_fill(0, 5, "%{$search}%"));
            });
        }

        $limit    = App::module('blog')->config('comments.comments_per_page');
        $count    = $query->count();
        $pages    = ceil($count / $limit);
        $page     = max(0, min($pages - 1, $page));
        $comments = array_values($query->offset($page * $limit)->related(['post' => function($query) {
            return $query->related('comments');
        }])->limit($limit)->orderBy('created', 'DESC')->get());

        foreach ($comments as $comment) {
            $comment->setContent(App::content()->applyPlugins($comment->getContent(), ['comment' => true]));
        }

        return compact('comments', 'pages');
    }

    /**
     * @Route("/", methods="POST")
     * @Route("/{id}", methods="POST", requirements={"id"="\d+"})
     * @Request({"comment": "array", "id": "int"}, csrf=true)
     */
    public function saveAction($data, $id = 0)
    {
        $user = App::user();

        if (!$id || !$comment = Comment::find($id)) {

            if ($id) {
                App::abort(400, __('Comment not found.'));
            }

            if (!$parent = Comment::find((int) @$data['parent_id'])) {
                App::abort(400, __('Invalid comment reply.'));
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

        unset($data['created']);

        $comment->save($data);

        return ['message' => $id ? __('Comment saved.') : __('Comment created.')];
    }

    /**
     * @Route("/{id}", methods="DELETE", requirements={"id"="\d+"})
     * @Request({"id": "int"}, csrf=true)
     */
    public function deleteAction($id)
    {
        if ($comment = Comment::find($id)) {
            $comment->delete();
        }

        return ['message' => __('Success')];
    }

    /**
     * @Route("/bulk", methods="POST")
     * @Request({"comments": "array"}, csrf=true)
     */
    public function bulkSaveAction($comments = [])
    {
        foreach ($comments as $data) {
            $this->saveAction($data, isset($data['id']) ? $data['id'] : 0);
        }

        return ['message' => __('Comments saved.')];
    }

    /**
     * @Route("/bulk", methods="DELETE")
     * @Request({"ids": "array"}, csrf=true)
     */
    public function bulkDeleteAction($ids = [])
    {
        foreach (array_filter($ids) as $id) {
            $this->deleteAction($id);
        }

        return ['message' => __('Comments deleted.')];
    }
}
