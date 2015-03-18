<?php

namespace Pagekit\Blog\Controller;

use Pagekit\Application as App;
use Pagekit\Application\Controller;
use Pagekit\Blog\Entity\Comment;

/**
 * @Access("blog: manage comments", admin=true)
 */
class CommentController extends Controller
{
    /**
     * @Request({"filter": "array", "post":"int", "page":"int"})
     * @Response("extensions/blog/views/admin/comment/index.php")
     */
    public function indexAction($filter = [], $post = 0, $page = 0)
    {
        App::view()->meta(['title' => $post ? __('Comments on %title%', ['%title%' => $post->getTitle()]) : __('Comments')]);
        App::view()->script('comment-index', 'extensions/blog/app/comment/index.js', ['vue-system', 'vue-validator', 'gravatar']);
        App::view()->style('comment-index', 'extensions/blog/assets/css/blog.admin.css');
        App::view()->data('comment', [
            'config' => [
                'filter' => $filter,
                'page'   => $page,
                'post'   => $post
            ],
            'data'   => [
                'statuses' => Comment::getStatuses()
            ]
        ]);
    }
}
