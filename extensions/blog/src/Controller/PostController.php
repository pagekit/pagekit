<?php

namespace Pagekit\Blog\Controller;

use Pagekit\Blog\Entity\Post;
use Pagekit\Component\Database\ORM\Repository;
use Pagekit\Framework\Controller\Controller;
use Pagekit\Framework\Controller\Exception;
use Pagekit\User\Entity\UserRepository;

/**
 * @Access("blog: manage content", admin=true)
 */
class PostController extends Controller
{
    const POSTS_PER_PAGE = 20;

    /**
     * @var Repository
     */
    protected $posts;

    /**
     * @var Repository
     */
    protected $roles;

    /**
     * @var UserRepository
     */
    protected $users;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->posts = $this('db.em')->getRepository('Pagekit\Blog\Entity\Post');
        $this->roles = $this('users')->getRoleRepository();
        $this->users = $this('users')->getUserRepository();
    }

    /**
     * @Request({"filter": "array", "page":"int"})
     * @View("blog/admin/post/index.razr")
     */
    public function indexAction($filter = null, $page = 0)
    {
        if ($filter) {
            $this('session')->set('blog.posts.filter', $filter);
        } else {
            $filter = $this('session')->get('blog.posts.filter', array());
        }

        $query = $this->posts->query();

        if (isset($filter['status']) && is_numeric($filter['status'])) {
            $query->where(array('status' => intval($filter['status'])));
        }

        if (isset($filter['search']) && strlen($filter['search'])) {
            $query->where(function($query) use ($filter) {
                $query->orWhere(array('title LIKE :search', 'slug LIKE :search'), array('search' => "%{$filter['search']}%"));
            });
        }

        $limit = self::POSTS_PER_PAGE;
        $count = $query->count();
        $total = ceil($count / $limit);
        $page  = max(0, min($total - 1, $page));

        $query->offset($page * $limit)->limit($limit)->related('user')->orderBy('date', 'DESC');

        if ($this('request')->isXmlHttpRequest()) {
            return $this('response')->json(array(
                'table' => $this('view')->render('view://blog/admin/post/table.razr', array('count' => $count, 'posts' => $query->get(), 'roles' => $this->roles->findAll())),
                'total' => $total
            ));
        }

        return array('head.title' => __('Posts'), 'posts' => $query->get(), 'statuses' => Post::getStatuses(), 'filter' => $filter, 'total' => $total, 'count' => $count);
    }

    /**
     * @View("blog/admin/post/edit.razr")
     */
    public function addAction()
    {
        $post = new Post;
        $post->setUser($this('user'));

        return array('head.title' => __('Add Post'), 'post' => $post, 'statuses' => Post::getStatuses(), 'roles' => $this->roles->findAll(), 'users' => $this->users->findAll());
    }

    /**
     * @Request({"id": "int"})
     * @View("blog/admin/post/edit.razr")
     */
    public function editAction($id)
    {
        try {

            if (!$post = $this->posts->query()->where(compact('id'))->related('user')->first()) {
                throw new Exception(__('Invalid post id.'));
            }

        } catch (Exception $e) {

            $this('message')->error($e->getMessage());

            return $this->redirect('@blog/post');
        }

        return array('head.title' => __('Edit Post'), 'post' => $post, 'statuses' => Post::getStatuses(), 'roles' => $this->roles->findAll(), 'users' => $this->users->findAll());
    }

    /**
     * @Request({"id": "int", "post": "array"})
     * @Token
     */
    public function saveAction($id, $data)
    {
        try {

            if (!$post = $this->posts->find($id)) {

                $post = new Post;

            }

            if (!$data['slug'] = $this->slugify($data['slug'] ?: $data['title'])) {
                throw new Exception('Invalid slug.');
            }

            $data['date'] = $this('dates')->getDateTime($data['date'])->setTimezone(new \DateTimeZone('UTC'));

            $this->posts->save($post, $data);

            $response = array('message' => $id ? __('Post saved.') : __('Post created.'), 'id' => $post->getId());

        } catch (Exception $e) {
            $response = array('message' => $e->getMessage(), 'error' => true);
        }

        return $this('response')->json($response);
    }

    /**
     * @Request({"ids": "int[]"})
     * @Token
     */
    public function deleteAction($ids = array())
    {
        foreach ($ids as $id) {
            if ($post = $this->posts->find($id)) {
                $this->posts->delete($post);
            }
        }

        return $this('response')->json(array('message' => _c('{0} No post deleted.|{1} Post deleted.|]1,Inf[ Posts deleted.', count($ids))));
    }

    /**
     * @Request({"ids": "int[]"})
     * @Token
     */
    public function copyAction($ids = array())
    {
        foreach ($ids as $id) {
            if ($post = $this->posts->find((int) $id)) {

                $post = clone $post;
                $post->setId(null);
                $post->setStatus(Post::STATUS_DRAFT);
                $post->setSlug($post->getSlug());
                $post->setTitle($post->getTitle().' - '.__('Copy'));

                $this->posts->save($post);
            }
        }

        return $this('response')->json(array('message' => _c('{0} No post copied.|{1} Post copied.|]1,Inf[ Posts copied.', count($ids))));
    }

    /**
     * @Request({"status": "int", "ids": "int[]"})
     * @Token
     */
    public function statusAction($status, $ids = array())
    {
        foreach ($ids as $id) {
            if ($post = $this->posts->find($id) and $post->getStatus() != $status) {
                $post->setStatus($status);
                $this->posts->save($post);
            }
        }

        if ($status == Post::STATUS_PUBLISHED) {
            $message = _c('{0} No post published.|{1} Post published.|]1,Inf[ Posts published.', count($ids));
        } else {
            $message = _c('{0} No post unpublished.|{1} Post unpublished.|]1,Inf[ Posts unpublished.', count($ids));
        }

        return $this('response')->json(compact('message'));
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
