<?php

namespace Pagekit\Blog\Controller;

use Pagekit\Application as App;
use Pagekit\Blog\Model\Post;

/**
 * @Access("blog: manage own posts || blog: manage all posts")
 * @Route("post", name="post")
 */
class PostApiController
{
    /**
     * @Route("/", methods="GET")
     * @Request({"filter": "array", "page":"int"})
     */
    public function indexAction($filter = [], $page = 0)
    {
        $query  = Post::query();
        $filter = array_merge(array_fill_keys(['status', 'search', 'author', 'order', 'limit'], ''), $filter);
        extract($filter, EXTR_SKIP);

        if(!App::user()->hasAccess('blog: manage all posts')) {
            $author = App::user()->getId();
        }

        if (is_numeric($status)) {
            $query->where(['status' => (int) $status]);
        }

        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->orWhere(['title LIKE :search', 'slug LIKE :search'], ['search' => "%{$search}%"]);
            });
        }

        if ($author) {
            $query->where(function ($query) use ($author) {
                $query->orWhere(['user_id' => (int) $author]);
            });
        }

        if (!preg_match('/^(date|title|comment_count)\s(asc|desc)$/i', $order, $order)) {
            $order = [1 => 'date', 2 => 'desc'];
        }

        $limit = (int) $limit ?: App::module('blog')->config('posts.posts_per_page');
        $count = $query->count();
        $pages = ceil($count / $limit);
        $page  = max(0, min($pages - 1, $page));

        $posts = array_values($query->offset($page * $limit)->related('user', 'comments')->limit($limit)->orderBy($order[1], $order[2])->get());

        return compact('posts', 'pages', 'count');
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
        if (!$id || !$post = Post::find($id)) {

            if ($id) {
                App::abort(404, __('Post not found.'));
            }

            $post = new Post;
        }

        if (!$data['slug'] = $this->slugify($data['slug'] ?: $data['title'])) {
            App::abort(400, __('Invalid slug.'));
        }

        // user without universal access can only edit their own posts
        if(!App::user()->hasAccess('blog: manage all posts') && $post->getUserId() !== App::user()->getId()) {
            return ['error' => __('Access denied.)'];
        }

        // user without universal access is not allowed to assign posts to other users
        if(!App::user()->hasAccess('blog: manage all posts')) {
            $data['user_id'] = App::user()->getId();
        }

        $data['date'] = App::intl()->date()->parse($data['date'])->setTimezone(new \DateTimeZone('UTC'));
        $data['comment_status'] = isset($data['comment_status']) ? $data['comment_status'] : 0;

        $post->save($data);

        return ['message' => 'success', 'post' => $post];
    }

    /**
     * @Route("/{id}", methods="DELETE", requirements={"id"="\d+"})
     * @Request({"id": "int"}, csrf=true)
     */
    public function deleteAction($id)
    {
        if ($post = Post::find($id)) {

            if(!App::user()->hasAccess('blog: manage all posts') && $post->getUserId() !== App::user()->getId()) {
                return ['error' => __('Access denied')];
            }

            $post->delete();
        }

        return ['message' => 'success'];
    }

    /**
     * @Request({"ids": "int[]"}, csrf=true)
     */
    public function copyAction($ids = [])
    {
        $count = 0;
        foreach ($ids as $id) {
            if ($post = Post::find((int) $id)) {
                if(!App::user()->hasAccess('blog: manage all posts') && $post->getUserId() !== App::user()->getId()) {
                    continue;
                }

                $post = clone $post;
                $post->setId(null);
                $post->setStatus(Post::STATUS_DRAFT);
                $post->setSlug($post->getSlug());
                $post->setTitle($post->getTitle().' - '.__('Copy'));
                $post->setCommentCount(0);
                $post->save();
                $count++;
            }
        }

        return ['message' => _c('{0} No post copied.|{1} Post copied.|]1,Inf[ Posts copied.', $count)];
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

        return ['message' => 'success'];
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

        return ['message' => 'success'];
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
