<?php

namespace Pagekit\Blog\Controller;

use Pagekit\Application as App;
use Pagekit\Application\Controller;
use Pagekit\Application\Exception;
use Pagekit\Blog\Entity\Post;
use Pagekit\User\Entity\Role;

/**
 * @Access("blog: manage content", admin=true)
 */
class PostController extends Controller
{
    /**
     * @Request({"filter": "array", "page":"int"})
     * @Response("extensions/blog/views/admin/post/index.php")
     */
    public function indexAction($filter = null, $page = 0)
    {
        App::view()->meta(['title' => __('Posts')]);
        App::view()->script('post-index', 'extensions/blog/app/post/index.js', ['vue-system']);
        App::view()->data('post', [
            'config' => [
                'filter' => $filter,
                'page'   => $page
            ],
            'data'   => [
                'statuses' => Post::getStatuses()
            ]
        ]);
    }

    /**
     * @Request({"id": "int"})
     * @Response("extensions/blog/views/admin/post/edit.php")
     */
    public function editAction($id = 0)
    {
        try {

            if (!$post = Post::where(compact('id'))->related('user')->first()) {

                if ($id) {
                    throw new Exception(__('Invalid post id.'));
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

            App::view()->meta(['title' => $id ? __('Edit Post') : __('Add Post')]);
            App::view()->script('post-edit', 'extensions/blog/app/post/edit.js', ['vue-system', 'vue-validator', 'uikit-datepicker', 'uikit-timepicker']);
            App::view()->data('post', [
                'data' => [
                    'post'     => $post,
                    'statuses' => Post::getStatuses(),
                    'roles'    => array_values(Role::findAll()),
                    'authors'  => App::db()->createQueryBuilder()
                        ->from('@system_user')
                        ->execute('id, username')
                        ->fetchAll()
                ]
            ]);

            return compact('post');

        } catch (Exception $e) {

            App::message()->error($e->getMessage());

            return $this->redirect('@blog/post');
        }
    }
}
