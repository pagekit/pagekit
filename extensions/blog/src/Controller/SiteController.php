<?php

namespace Pagekit\Blog\Controller;

use Pagekit\Application as App;
use Pagekit\Blog\BlogExtension;
use Pagekit\Blog\Entity\Comment;
use Pagekit\Blog\Entity\Post;
use Pagekit\Comment\Event\CommentEvent;
use Pagekit\Database\Event\EntityEvent;
use Pagekit\Framework\Controller\Controller;
use Pagekit\Framework\Controller\Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Route("/")
 */
class SiteController extends Controller
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

        $autoclose = $this->extension->getParams('comments.autoclose') ? $this->extension->getParams('comments.autoclose.days') : 0;

        App::on('blog.post.postLoad', function (EntityEvent $event) use ($autoclose) {
            $post = $event->getEntity();
            $post->setCommentable($post->getCommentStatus() && (!$autoclose or $post->getDate() >= new \DateTime("-{$autoclose} day")));
        });
    }

    /**
     * @Route("/", name="site")
     * @Route("/page/{page}", name="page", requirements={"page" = "\d+"})
     * @Response("extensions/blog/views/post/index.razr")
     */
    public function indexAction($page = 1)
    {
        App::on('blog.post.postLoad', function (EntityEvent $event) {
            $post = $event->getEntity();
            $post->setContent(App::content()->applyPlugins($post->getContent(), ['post' => $post, 'markdown' => $post->get('markdown'), 'readmore' => true]));
        });

        $query = Post::where(['status = ?', 'date < ?'], [Post::STATUS_PUBLISHED, new \DateTime])->related('user');

        if (!$limit = $this->extension->getParams('posts_per_page')) {
            $limit = 10;
        }

        $count = $query->count();
        $total = ceil($count / $limit);
        $page  = max(1, min($total, $page));

        $query->offset(($page - 1) * $limit)->limit($limit)->orderBy('date', 'DESC');

        return [
            'head.title'          => __('Blog'),
            'head.link.alternate' => [
                'href'  => App::url()->route('@blog/site/feed', [], true),
                'title' => App::option()->get('system:app.site_title'),
                'type'  => App::feed()->create($this->extension->getParams('feed.type'))->getMIMEType()
            ],
            'posts'               => $query->get(),
            'params'              => $this->extension->getParams(),
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
                throw new Exception(__('Insufficient User Rights.'));
            }

            // check minimum idle time in between user comments
            if (!$user->hasAccess('blog: skip comment min idle')
                and $minidle = $this->extension->getParams('comments.minidle')
                and $comment = Comment::where($user->isAuthenticated() ? ['user_id' => $user->getId()] : ['ip' => App::request()->getClientIp()])->orderBy('created', 'DESC')->first()
            ) {

                $diff = $comment->getCreated()->diff(new \DateTime("- {$minidle} sec"));

                if ($diff->invert) {
                    throw new Exception(__('Please wait another %seconds% seconds before commenting again.', ['%seconds%' => $diff->s + $diff->i * 60 + $diff->h * 3600]));
                }
            }

            if (!$post = Post::where(['id' => $id, 'status' => Post::STATUS_PUBLISHED])->first()) {
                throw new Exception(__('Insufficient User Rights.'));
            }

            if (!$post->isCommentable()) {
                throw new Exception(__('Comments have been disabled for this post.'));
            }

            // retrieve user data
            if ($user->isAuthenticated()) {
                $data['author'] = $user->getName();
                $data['email']  = $user->getEmail();
                $data['url']    = $user->getUrl();
            } elseif ($this->extension->getParams('comments.require_name_and_email') && (!$data['author'] || !$data['email'])) {
                throw new Exception(__('Please provide valid name and email.'));
            }

            $comment = new Comment;
            $comment->setUserId((int)$user->getId());
            $comment->setIp(App::request()->getClientIp());
            $comment->setCreated(new \DateTime);
            $comment->setPost($post);

            $approved_once = (boolean)Comment::where(['user_id' => $user->getId(), 'status' => Comment::STATUS_APPROVED])->first();
            $comment->setStatus($user->hasAccess('blog: skip comment approval') ? Comment::STATUS_APPROVED : $user->hasAccess('blog: comment approval required once') && $approved_once ? Comment::STATUS_APPROVED : Comment::STATUS_PENDING);

            // check the max links rule
            if ($comment->getStatus() == Comment::STATUS_APPROVED && $this->extension->getParams('comments.maxlinks') <= preg_match_all('/<a [^>]*href/i', @$data['content'])) {
                $comment->setStatus(Comment::STATUS_PENDING);
            }

            // check for spam
            App::events()->dispatch('system.comment.spam_check', new CommentEvent($comment));

            $comment->save($data);

            App::message()->info(__('Thanks for commenting!'));

            return $this->redirect(App::url()->route('@blog/id', ['id' => $post->getId()], true).'#comment-'.$comment->getId());

        } catch (Exception $e) {

            $message = $e->getMessage();

        } catch (\Exception $e) {

            $message = __('Whoops, something went wrong!');

        }

        App::message()->error($message);

        return $this->redirect(App::url()->previous());
    }

    /**
     * @Route("/{id}", name="id")
     * @Response("extensions/blog/views/post/post.razr")
     */
    public function postAction($id = 0)
    {
        if (!$post = Post::where(['id = ?', 'status = ?', 'date < ?'], [$id, Post::STATUS_PUBLISHED, new \DateTime])->related('user')->first()) {
            throw new NotFoundHttpException(__('Post with id "%id%" not found!', ['%id%' => $id]));
        }

        if (!$post->hasAccess(App::user())) {
            throw new AccessDeniedHttpException(__('Unable to access this post!'));
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

        return ['head.title' => __($post->getTitle()), 'post' => $post, 'params' => $this->extension->getParams()];
    }

    /**
     * @Route("/feed")
     * @Route("/feed/{type}")
     */
    public function feedAction($type = '')
    {
        $feed = App::feed()->create($type ?: $this->extension->getParams('feed.type'), [
            'title'       => App::option()->get('system:app.site_title'),
            'link'        => App::url()->route('@blog/site', [], true),
            'description' => App::option()->get('system:app.site_description'),
            'element'     => ['language', App::option()->get('system:app.locale')],
            'selfLink'    => App::url()->route('@blog/site/feed', [], true)
        ]);

        if ($last = Post::where(['status = ?', 'date < ?'], [Post::STATUS_PUBLISHED, new \DateTime])->limit(1)->orderBy('modified', 'DESC')->first()) {
            $feed->setDate($last->getModified());
        }

        foreach (Post::where(['status = ?', 'date < ?'], [Post::STATUS_PUBLISHED, new \DateTime])->related('user')->limit($this->extension->getParams('feed.limit'))->orderBy('date', 'DESC')->get() as $post) {
            $feed->addItem(
                $feed->createItem([
                    'title'       => $post->getTitle(),
                    'link'        => App::url()->route('@blog/id', ['id' => $post->getId()], true),
                    'description' => App::content()->applyPlugins($post->getContent(), ['post' => $post, 'markdown' => $post->get('markdown'), 'readmore' => true]),
                    'date'        => $post->getDate(),
                    'author'      => [$post->getUser()->getName(), $post->getUser()->getEmail()],
                    'id'          => App::url()->route('@blog/id', ['id' => $post->getId()], true)
                ])
            );
        }

        return App::response()->create($feed->generate(), Response::HTTP_OK, ['Content-Type' => $feed->getMIMEType()]);
    }
}
