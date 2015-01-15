<?php

namespace Pagekit\Blog\Controller;

use Pagekit\Application as App;
use Pagekit\Blog\BlogExtension;
use Pagekit\Blog\Entity\Comment;
use Pagekit\Blog\Entity\Post;
use Pagekit\Framework\Controller\Controller;
use Pagekit\Framework\Controller\Exception;
use Pagekit\User\Entity\Role;
use Pagekit\User\Entity\User;

/**
 * @Access("blog: manage content", admin=true)
 */
class PostController extends Controller
{
    /**
     * @var BlogExtension
     */
    protected $extension;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->extension = App::extensions()->get('blog');
    }

    /**
     * @Request({"filter": "array", "page":"int"})
     * @Response("extensions/blog/views/admin/post/index.razr")
     */
    public function indexAction($filter = null, $page = 0)
    {
        if ($filter) {
            App::session()->set('blog.posts.filter', $filter);
        } else {
            $filter = App::session()->get('blog.posts.filter', []);
        }

        $query = Post::query();

        if (isset($filter['status']) && is_numeric($filter['status'])) {
            $query->where(['status' => intval($filter['status'])]);
        }

        if (isset($filter['search']) && strlen($filter['search'])) {
            $query->where(function($query) use ($filter) {
                $query->orWhere(['title LIKE :search', 'slug LIKE :search'], ['search' => "%{$filter['search']}%"]);
            });
        }

        $limit = $this->extension->getParams('posts.posts_per_page');
        $count = $query->count();
        $total = ceil($count / $limit);
        $page  = max(0, min($total - 1, $page));
        $posts = $query->offset($page * $limit)->limit($limit)->related('user')->orderBy('date', 'DESC')->get();

        if ($posts) {
            $pending = App::db()->createQueryBuilder()
                ->from('@blog_comment')
                ->where(['status' => Comment::STATUS_PENDING])
                ->whereIn('post_id', array_keys($posts))
                ->groupBy('post_id')
                ->execute('post_id, count(id)')
                ->fetchAll(\PDO::FETCH_KEY_PAIR);
        } else {
            $pending = [];
        }

        if (App::request()->isXmlHttpRequest()) {
            return App::response()->json([
                'table' => App::view()->render('extensions/blog/views/admin/post/table.razr', ['count' => $count, 'posts' => $posts, 'roles' => Role::findAll(), 'pending' => $pending]),
                'total' => $total
            ]);
        }

        return ['head.title' => __('Posts'), 'posts' => $posts, 'statuses' => Post::getStatuses(), 'filter' => $filter, 'total' => $total, 'count' => $count, 'pending' => $pending];
    }

    /**
     * @Response("extensions/blog/views/admin/post/edit.razr")
     */
    public function addAction()
    {
        $post = new Post;
        $post->setUser(App::user());
        $post->setCommentStatus((bool) $this->extension->getParams('posts.comments_enabled'));
        $post->set('title', $this->extension->getParams('posts.show_title'));
        $post->set('markdown', $this->extension->getParams('posts.markdown_enabled'));

        return ['head.title' => __('Add Post'), 'post' => $post, 'statuses' => Post::getStatuses(), 'roles' => Role::findAll(), 'users' => User::findAll()];
    }

    /**
     * @Request({"id": "int"})
     * @Response("extensions/blog/views/admin/post/edit.razr")
     */
    public function editAction($id)
    {
        try {

            if (!$post = Post::where(compact('id'))->related('user')->first()) {
                throw new Exception(__('Invalid post id.'));
            }

        } catch (Exception $e) {

            App::message()->error($e->getMessage());

            return $this->redirect('@blog/post');
        }

        return ['head.title' => __('Edit Post'), 'post' => $post, 'statuses' => Post::getStatuses(), 'roles' => Role::findAll(), 'users' => User::findAll()];
    }

    /**
     * @Request({"id": "int", "post": "array"}, csrf=true)
     * @Response("json")
     */
    public function saveAction($id, $data)
    {
        try {

            if (!$post = Post::find($id)) {
                $post = new Post;
            }

            if (!$data['slug'] = $this->slugify($data['slug'] ?: $data['title'])) {
                throw new Exception('Invalid slug.');
            }

            $data['date'] = App::dates()->getDateTime($data['date'])->setTimezone(new \DateTimeZone('UTC'));
            $data['comment_status'] = isset($data['comment_status']) ? $data['comment_status'] : 0;

            $post->save($data);

            return ['message' => $id ? __('Post saved.') : __('Post created.'), 'id' => $post->getId()];

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
            if ($post = Post::find($id)) {
                $post->delete();
            }
        }

        return ['message' => _c('{0} No post deleted.|{1} Post deleted.|]1,Inf[ Posts deleted.', count($ids))];
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
     * @Request({"status": "int", "ids": "int[]"}, csrf=true)
     * @Response("json")
     */
    public function statusAction($status, $ids = [])
    {
        foreach ($ids as $id) {
            if ($post = Post::find($id) and $post->getStatus() != $status) {
                $post->setStatus($status);
                $post->save();
            }
        }

        if ($status == Post::STATUS_PUBLISHED) {
            $message = _c('{0} No post published.|{1} Post published.|]1,Inf[ Posts published.', count($ids));
        } else {
            $message = _c('{0} No post unpublished.|{1} Post unpublished.|]1,Inf[ Posts unpublished.', count($ids));
        }

        return compact('message');
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
