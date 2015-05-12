<?php

namespace Pagekit\Blog\Controller;

use Pagekit\Application as App;
use Pagekit\Blog\Entity\Comment;
use Pagekit\Blog\Entity\Post;

/**
 * @Access("blog: manage comments", admin=true)
 */
class CommentController
{
    /**
     * @Request({"filter": "array", "post":"int", "page":"int"})
     */
    public function indexAction($filter = [], $post = 0, $page = 0)
    {
        $post = Post::find($post);

        return [
            '$view' => [
                'title' => $post ? __('Comments on %title%', ['%title%' => $post->getTitle()]) : __('Comments'),
                'name'  => 'blog:views/admin/comment/index.php'
            ],
            '$data'   => [
                'statuses' => Comment::getStatuses(),
                'config'   => [
                    'filter' => $filter,
                    'page'   => $page,
                    'post'   => $post
                ]
            ]
        ];
    }
}
