<?php

namespace Pagekit\Blog\Controller;

use Pagekit\Application as App;
use Pagekit\Blog\Entity\Post;
use Pagekit\User\Entity\Role;

/**
 * @Access("blog: manage content", admin=true)
 */
class PostController
{
    /**
     * @Request({"filter": "array", "page":"int"})
     */
    public function indexAction($filter = null, $page = 0)
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
}
