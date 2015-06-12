<?php

namespace Pagekit\Blog\Controller;

use Pagekit\Application as App;
use Pagekit\Blog\BlogExtension;
use Pagekit\Blog\Entity\Comment;
use Pagekit\Blog\Entity\Post;
use Pagekit\Comment\Event\CommentEvent;
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
        $this->module = App::module('blog');
        $autoclose = $this->module->config('comments.autoclose') ? $this->module->config('comments.autoclose_days') : 0;

        App::on('blog.post.postLoad', function (EntityEvent $event) use ($autoclose) {
            $post = $event->getEntity();
            $post->setCommentable($post->getCommentStatus() && (!$autoclose or $post->getDate() >= new \DateTime("-{$autoclose} day")));
        });
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

        if (!$limit = $this->module->config('posts_per_page')) {
            $limit = 10;
        }

        $count = $query->count();
        $total = ceil($count / $limit);
        $page  = max(1, min($total, $page));

        $query->offset(($page - 1) * $limit)->limit($limit)->orderBy('date', 'DESC');

        return [
            '$view' => [
                'title' => __('Blog'),
                'name'  => 'blog:views/site/post-index.php',
                'link'  => [
                    'alternate' => [
                        'href'  => App::url('@blog/site/feed', [], true),
                        'title' => App::system()->config('site.title'),
                        'type'  => App::feed()->create($this->module->config('feed.type'))->getMIMEType()
                    ]
                ]
            ],
            'posts'               => $query->get(),
            'params'              => $this->module->config,
            'total'               => $total,
            'page'                => $page
        ];
    }

    /**
     * @Route("/comment")
     * @Request({"post_id": "int", "comment": "array"}, csrf=true)
     */
    public function commentAction($id, $data)
    {
        try {

            $user = App::user();

            if (!$user->hasAccess('blog: post comments')) {
                App::abort(403, __('Insufficient User Rights.'));
            }

            // check minimum idle time in between user comments
            if (!$user->hasAccess('blog: skip comment min idle')
                and $minidle = $this->module->config('comments.minidle')
                and $comment = Comment::where($user->isAuthenticated() ? ['user_id' => $user->getId()] : ['ip' => App::request()->getClientIp()])->orderBy('created', 'DESC')->first()
            ) {

                $diff = $comment->getCreated()->diff(new \DateTime("- {$minidle} sec"));

                if ($diff->invert) {
                    App::abort(429, __('Please wait another %seconds% seconds before commenting again.', ['%seconds%' => $diff->s + $diff->i * 60 + $diff->h * 3600]));
                }
            }

            if (!$post = Post::where(['id' => $id, 'status' => Post::STATUS_PUBLISHED])->first()) {
                App::abort(403, __('Insufficient User Rights.'));
            }

            if (!$post->isCommentable()) {
                App::abort(403, __('Comments have been disabled for this post.'));
            }

            // retrieve user data
            if ($user->isAuthenticated()) {
                $data['author'] = $user->getName();
                $data['email']  = $user->getEmail();
                $data['url']    = $user->getUrl();
            } elseif ($this->module->config('comments.require_name_and_email') && (!$data['author'] || !$data['email'])) {
                App::abort(400, __('Please provide valid name and email.'));
            }

            $comment = new Comment;
            $comment->setUserId((int)$user->getId());
            $comment->setIp(App::request()->getClientIp());
            $comment->setCreated(new \DateTime);
            $comment->setPost($post);

            $approved_once = (boolean) Comment::where(['user_id' => $user->getId(), 'status' => Comment::STATUS_APPROVED])->first();
            $comment->setStatus($user->hasAccess('blog: skip comment approval') ? Comment::STATUS_APPROVED : $user->hasAccess('blog: comment approval required once') && $approved_once ? Comment::STATUS_APPROVED : Comment::STATUS_PENDING);

            // check the max links rule
            if ($comment->getStatus() == Comment::STATUS_APPROVED && $this->module->config('comments.maxlinks') <= preg_match_all('/<a [^>]*href/i', @$data['content'])) {
                $comment->setStatus(Comment::STATUS_PENDING);
            }

            // check for spam
            //App::trigger('system.comment.spam_check', new CommentEvent($comment));

            $comment->save($data);

            return $comment;

        } catch (\Exception $e) {

            App::abort(500, $e->getMessage());
        }
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

        $query = Comment::where(['status = ?'], [Comment::STATUS_APPROVED])->orderBy('created');

        if (App::user()->isAuthenticated()) {
            $query->orWhere(function ($query) {
                $query->where(['status = ?', 'user_id = ?'], [Comment::STATUS_PENDING, App::user()->getId()]);
            });
        }

        App::get('db.em')->related($post, 'comments', $query);

        $post->setContent(App::content()->applyPlugins($post->getContent(), ['post' => $post, 'markdown' => $post->get('markdown')]));

        foreach ($post->getComments() as $comment) {
            $comment->setContent(App::content()->applyPlugins($comment->getContent(), ['comment' => true]));
        }

        return [
            '$view' => [
                'title' => __($post->getTitle()),
                'name'  => 'blog:views/site/post.php'
            ],
            'post' => $post,
            'blog' => $this->module,
            '$comments' => [
                'user' => App::user(),
                'enabled' => $post->isCommentable(),
                'access' => [
                    'view' => App::user()->hasAccess('blog: view comments'),
                    'post' => App::user()->hasAccess('blog: post comments'),
                ],
                'entries' => array_values($post->getComments()),
                'requireinfo' => $this->module->config('comments.require_name_and_email')
            ]
        ];
    }

    /**
     * @Route("/feed")
     * @Route("/feed/{type}")
     */
    public function feedAction($type = '')
    {
        $feed = App::feed()->create($type ?: $this->module->config('feed.type'), [
            'title'       => App::system()->config('site.title'),
            'link'        => App::url('@blog/site', [], true),
            'description' => App::system()->config('site.description'),
            'element'     => ['language', App::module('system/locale')->config('locale')],
            'selfLink'    => App::url('@blog/site/feed', [], true)
        ]);

        if ($last = Post::where(['status = ?', 'date < ?'], [Post::STATUS_PUBLISHED, new \DateTime])->limit(1)->orderBy('modified', 'DESC')->first()) {
            $feed->setDate($last->getModified());
        }

        foreach (Post::where(['status = ?', 'date < ?'], [Post::STATUS_PUBLISHED, new \DateTime])->related('user')->limit($this->module->config('feed.limit'))->orderBy('date', 'DESC')->get() as $post) {
            $feed->addItem(
                $feed->createItem([
                    'title'       => $post->getTitle(),
                    'link'        => App::url('@blog/id', ['id' => $post->getId()], true),
                    'description' => App::content()->applyPlugins($post->getContent(), ['post' => $post, 'markdown' => $post->get('markdown'), 'readmore' => true]),
                    'date'        => $post->getDate(),
                    'author'      => [$post->getUser()->getName(), $post->getUser()->getEmail()],
                    'id'          => App::url('@blog/id', ['id' => $post->getId()], true)
                ])
            );
        }

        return App::response($feed->generate(), 200, ['Content-Type' => $feed->getMIMEType()]);
    }
}
