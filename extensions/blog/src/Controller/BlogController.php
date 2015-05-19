<?php

namespace Pagekit\Blog\Controller;

use Pagekit\Application as App;
use Pagekit\Blog\Entity\Comment;
use Pagekit\Blog\Entity\Post;
use Pagekit\User\Entity\Role;

class BlogController
{
    /**
     * @Access("blog: manage content", admin=true)
     * @Request({"filter": "array", "page":"int"})
     */
    public function postAction($filter = null, $page = 0)
    {
        return [
            '$view' => [
                'title' => __('Posts'),
                'name'  => 'blog:views/admin/post/index.php'
            ],
            '$data' => [
                'statuses' => Post::getStatuses(),
                'config'   => [
                    'filter' => $filter,
                    'page'   => $page
                ]
            ]
        ];
    }

    /**
     * @Route("/post/edit", name="post/edit")
     * @Access("blog: manage content", admin=true)
     * @Request({"id": "int"})
     */
    public function editAction($id = 0)
    {
        try {

            if (!$post = Post::where(compact('id'))->related('user')->first()) {

                if ($id) {
                    App::abort(404, __('Invalid post id.'));
                }

                $module = App::module('blog');

                $post = new Post;
                $post->setUser(App::user());
                $post->setStatus(Post::STATUS_DRAFT);
                $post->setDate(new \DateTime);
                $post->setUser(App::user());
                $post->setCommentStatus((bool) $module->config('posts.comments_enabled'));
                $post->set('title', $module->config('posts.show_title'));
                $post->set('markdown', $module->config('posts.markdown_enabled'));
            }

            return [
                '$view' => [
                    'title' => $id ? __('Edit Post') : __('Add Post'),
                    'name'  => 'blog:views/admin/post/edit.php'
                ],
                '$data' => [
                    'post'     => $post,
                    'statuses' => Post::getStatuses(),
                    'roles'    => array_values(Role::findAll()),
                    'authors'  => App::db()->createQueryBuilder()
                        ->from('@system_user')
                        ->execute('id, username')
                        ->fetchAll()
                ],
                'post' => $post
            ];

        } catch (\Exception $e) {

            App::message()->error($e->getMessage());

            return App::redirect('@blog/post');
        }
    }

    /**
     * @Access("blog: manage comments", admin=true)
     * @Request({"filter": "array", "post":"int", "page":"int"})
     */
    public function commentAction($filter = [], $post = 0, $page = 0)
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

    /**
     * @Access("blog: manage settings", admin=true)
     */
    public function settingsAction()
    {
        return [
            '$view' => [
                'title' => __('Blog Settings'),
                'name'  => 'blog:views/admin/settings.php'
            ],
            '$data' => [
                'config' => App::module('blog')->config()
            ]
        ];
    }
}
