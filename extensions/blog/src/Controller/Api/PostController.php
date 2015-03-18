<?php

namespace Pagekit\Blog\Controller\Api;

use Pagekit\Application as App;
use Pagekit\Application\Controller;
use Pagekit\Application\Exception;
use Pagekit\Blog\Entity\Post;

/**
 * @Access("blog: manage content")
 * @Response("json")
 */
class PostController extends Controller
{
    /**
     * @Route("/", methods="GET")
     * @Request({"filter": "array", "page":"int"})
     */
    public function indexAction($filter = [], $page = 0)
    {
        $query = Post::query();
        $filter = array_merge(array_fill_keys(['status', 'search'], ''), $filter);
        extract($filter, EXTR_SKIP);

        if (is_numeric($status)) {
            $query->where(['status' => (int) $status]);
        }

        if ($search) {
            $query->where(function($query) use ($search) {
                $query->orWhere(['title LIKE :search', 'slug LIKE :search'], ['search' => "%{$search}%"]);
            });
        }

        $limit = App::module('blog')->config('posts.posts_per_page');
        $count = $query->count();
        $pages = ceil($count / $limit);
        $page  = max(0, min($pages - 1, $page));

        $posts = array_values($query->offset($page * $limit)->related('user', 'comments')->limit($limit)->orderBy('date', 'DESC')->get());

        return compact('posts', 'pages');
    }

    /**
     * @Route("/{id}", methods="GET", requirements={"id"="\d+"})
     */
    public function getAction($id)
    {
        return Post::where(compact('id'))->related('user', 'comments')->first();
    }

    /**
     * @Route("/", methods="POST")
     * @Route("/{id}", methods="POST", requirements={"id"="\d+"})
     * @Request({"post": "array", "id": "int"}, csrf=true)
     */
    public function saveAction($data, $id = 0)
    {
        try {

            if (!$id || !$post = Post::find($id)) {

                if ($id) {
                    throw new Exception('Post not found.');
                }

                $post = new Post;
            }

            if (!$data['slug'] = $this->slugify($data['slug'] ?: $data['title'])) {
                throw new Exception('Invalid slug.');
            }

            $data['date'] = App::dates()->getDateTime($data['date'])->setTimezone(new \DateTimeZone('UTC'));
            $data['comment_status'] = isset($data['comment_status']) ? $data['comment_status'] : 0;

            $post->save($data);

            return ['message' => $id ? __('Post saved.') : __('Post created.'), 'post' => $post];

        } catch (Exception $e) {

            return ['message' => $e->getMessage(), 'error' => true];

        }
    }

    /**
     * @Route("/{id}", methods="DELETE", requirements={"id"="\d+"})
     * @Request({"id": "int"}, csrf=true)
     */
    public function deleteAction($id)
    {
        try {

            if ($post = Post::find($id)) {
                $post->delete();
            }

        } catch (Exception $e) {
            return ['message' => $e->getMessage(), 'error' => true];
        }

        return ['message' => __('Success')];
    }

    /**
     * @Request({"ids": "int[]"}, csrf=true)
     * @Response("json")
     */
    public function copyAction($ids = [])
    {
        foreach ($ids as $id) {
            if ($post = Post::find((int) $id)) {
                $post = clone $post;
                $post->setId(null);
                $post->setStatus(Post::STATUS_DRAFT);
                $post->setSlug($post->getSlug());
                $post->setTitle($post->getTitle().' - '.__('Copy'));
                $post->setCommentCount(0);
                $post->save();
            }
        }

        return ['message' => _c('{0} No post copied.|{1} Post copied.|]1,Inf[ Posts copied.', count($ids))];
    }

    /**
     * @Route("/bulk", methods="POST")
     * @Request({"posts": "array"}, csrf=true)
     */
    public function bulkSaveAction($posts = [])
    {
        foreach ($posts as $data) {
            $this->saveAction($data, isset($data['id']) ? $data['id'] : 0);
        }

        return ['message' => __('Posts saved.')];
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

        return ['message' => __('Posts deleted.')];
    }

    protected function slugify($slug)
    {
        $slug = preg_replace('/\xE3\x80\x80/', ' ', $slug);
        $slug = str_replace('-', ' ', $slug);
        $slug = preg_replace('#[:\#\*"@+=;!><&\.%()\]\/\'\\\\|\[]#', "\x20", $slug);
        $slug = str_replace('?', '', $slug);
        $slug = trim(mb_strtolower($slug, 'UTF-8'));
        $slug = preg_replace('#\x20+#', '-', $slug);

        return $slug;
    }
}
