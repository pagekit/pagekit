<?php

namespace Pagekit\Blog\Controller;

use Pagekit\Application as App;
use Pagekit\Application\Controller;
use Pagekit\Blog\Entity\Comment;
use Pagekit\Blog\Entity\Post;

/**
 * @Access("blog: manage comments", admin=true)
 */
class CommentController extends Controller
{
    /**
     * @Request({"filter": "array", "post":"int", "page":"int"})
     * @Response("blog: views/admin/comment/index.php")
     */
    public function indexAction($filter = [], $post = 0, $page = 0)
    {
        $post = Post::find($post);

        return [
            '$meta' => [
                'title' => $post ? __('Comments on %title%', ['%title%' => $post->getTitle()]) : __('Comments')
            ],
            '$config' => [
                'filter' => $filter,
                'page'   => $page,
                'post'   => $post
            ],
            '$data'   => [
                'statuses' => Comment::getStatuses()
            ]
        ];
    }
}
