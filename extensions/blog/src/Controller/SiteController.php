<?php

namespace Pagekit\Blog\Controller;

use Pagekit\Application as App;
use Pagekit\Blog\BlogExtension;
use Pagekit\Blog\Model\Post;
use Pagekit\Database\Event\EntityEvent;

class SiteController
{
    /**
     * @var BlogExtension
     */
    protected $blog;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->blog = App::module('blog');
    }

    /**
     * @Route("/")
     * @Route("/page/{page}", name="page", requirements={"page" = "\d+"})
     */
    public function indexAction($page = 1)
    {
        App::on('blog.post.postLoad', function (EntityEvent $event) {
            $post = $event->getEntity();
            $post->setContent(App::content()->applyPlugins($post->getContent(), ['post' => $post, 'markdown' => $post->get('markdown'), 'readmore' => true]));
        });

        $query = Post::where(['status = ?', 'date < ?'], [Post::STATUS_PUBLISHED, new \DateTime])->related('user');

        if (!$limit = $this->blog->config('posts_per_page')) {
            $limit = 10;
        }

        $count = $query->count();
        $total = ceil($count / $limit);
        $page  = max(1, min($total, $page));

        $query->offset(($page - 1) * $limit)->limit($limit)->orderBy('date', 'DESC');

        return [
            '$view' => [
                'title' => __('Blog'),
                'name' => 'blog:views/site/post-index.php',
                'link' => [
                    'alternate' => [
                        'href' => App::url('@blog/site/feed', [], true),
                        'title' => App::module('system/site')->config('title'),
                        'type' => App::feed()->create($this->blog->config('feed.type'))->getMIMEType()
                    ]
                ]
            ],
            'posts' => $query->get(),
            'params' => $this->blog->config(),
            'total' => $total,
            'page' => $page
        ];
    }

    /**
     * @Route("/{id}", name="id")
     */
    public function postAction($id = 0)
    {
        if (!$post = Post::where(['id = ?', 'status = ?', 'date < ?'], [$id, Post::STATUS_PUBLISHED, new \DateTime])->related('user')->first()) {
            App::abort(404, __('Post with id "%id%" not found!', ['%id%' => $id]));
        }

        if (!$post->hasAccess(App::user())) {
            App::abort(403, __('Unable to access this post!'));
        }

        $post->setContent(App::content()->applyPlugins($post->getContent(), ['post' => $post, 'markdown' => $post->get('markdown')]));
        $user = App::user();

        return [
            '$view' => [
                'title' => __($post->getTitle()),
                'name' => 'blog:views/site/post.php'
            ],
            'post' => $post,
            'blog' => $this->blog,
            '$comments' => [
                'config' => [
                    'post' => $post->getId(),
                    'enabled' => $post->isCommentable(),
                    'requireinfo' => $this->blog->config('comments.require_name_and_email'),
                    'max_depth' => $this->blog->config('comments.max_depth')
                ],
                'user' => [
                    'name' => $user->getName(),
                    'isAuthenticated' => $user->isAuthenticated(),
                    'canView' => $user->hasAccess('blog: view comments'),
                    'canComment' => $user->hasAccess('blog: post comments'),
                ],

            ]
        ];
    }

    /**
     * @Route("/feed")
     * @Route("/feed/{type}")
     */
    public function feedAction($type = '')
    {
        $site = App::module('system/site');
        $feed = App::feed()->create($type ?: $this->blog->config('feed.type'), [
            'title' => $site->config('title'),
            'link' => App::url('@blog/site', [], true),
            'description' => $site->config('description'),
            'element' => ['language', App::module('system/locale')->config('locale')],
            'selfLink' => App::url('@blog/site/feed', [], true)
        ]);

        if ($last = Post::where(['status = ?', 'date < ?'], [Post::STATUS_PUBLISHED, new \DateTime])->limit(1)->orderBy('modified', 'DESC')->first()) {
            $feed->setDate($last->getModified());
        }

        foreach (Post::where(['status = ?', 'date < ?'], [Post::STATUS_PUBLISHED, new \DateTime])->related('user')->limit($this->blog->config('feed.limit'))->orderBy('date', 'DESC')->get() as $post) {
            $feed->addItem(
                $feed->createItem([
                    'title' => $post->getTitle(),
                    'link' => App::url('@blog/id', ['id' => $post->getId()], true),
                    'description' => App::content()->applyPlugins($post->getContent(), ['post' => $post, 'markdown' => $post->get('markdown'), 'readmore' => true]),
                    'date' => $post->getDate(),
                    'author' => [$post->getUser()->getName(), $post->getUser()->getEmail()],
                    'id' => App::url('@blog/id', ['id' => $post->getId()], true)
                ])
            );
        }

        return App::response($feed->generate(), 200, ['Content-Type' => $feed->getMIMEType()]);
    }
}
