<?php

namespace Pagekit\Blog\Controller;

use FeedWriter\ATOM;
use FeedWriter\Feed;
use FeedWriter\RSS1;
use FeedWriter\RSS2;
use Pagekit\Blog\BlogExtension;
use Pagekit\Blog\Entity\Comment;
use Pagekit\Blog\Entity\Post;
use Pagekit\Comment\Event\CommentEvent;
use Pagekit\Component\Database\ORM\Repository;
use Pagekit\Framework\Controller\Controller;
use Pagekit\Framework\Controller\Exception;
use Pagekit\Framework\Database\Event\EntityEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Route("/blog")
 */
class SiteController extends Controller
{
    /**
     * @var BlogExtension
     */
    protected $extension;

    /**
     * @var Repository
     */
    protected $posts;

    /**
     * @var Repository
     */
    protected $comments;

    /**
     * Constructor.
     *
     * @param BlogExtension $extension
     */
    public function __construct(BlogExtension $extension)
    {
        $this->extension = $extension;
        $this->posts     = $this['db.em']->getRepository('Pagekit\Blog\Entity\Post');
        $this->comments  = $this['db.em']->getRepository('Pagekit\Blog\Entity\Comment');

        $autoclose = $this->extension->getParams('comments.autoclose') ? $this->extension->getParams('comments.autoclose.days') : 0;

        $this['events']->addListener('blog.post.postLoad', function(EntityEvent $event) use ($autoclose) {
            $post = $event->getEntity();
            $post->setCommentable($post->getCommentStatus() && (!$autoclose or $post->getDate() >= new \DateTime("-{$autoclose} day")));
        });
    }

    /**
     * @Route("/page/{page}", name="@blog/page", requirements={"page" = "\d+"})
     * @Route("/", name="@blog/site")
     * @Response("extension://blog/views/post/index.razr")
     */
    public function indexAction($page = 1)
    {
        $this['events']->addListener('blog.post.postLoad', function(EntityEvent $event) {
            $post = $event->getEntity();
            $post->setContent($this['content']->applyPlugins($post->getContent(), ['post' => $post, 'markdown' => $post->get('markdown'), 'readmore' => true]));
        });

        $query = $this->posts->query()->where(['status = ?', 'date < ?'], [Post::STATUS_PUBLISHED, new \DateTime])->related('user');

        if (!$limit = $this->extension->getParams('posts_per_page')) {
            $limit = 10;
        }

        $count = $query->count();
        $total = ceil($count / $limit);
        $page  = max(1, min($total, $page));

        $query->offset(($page-1) * $limit)->limit($limit)->orderBy('date', 'DESC');

        return [
            'head.title'          => __('Blog'),
            'head.link.alternate' => ['href' => $this['url']->route('@blog/site/feed', [], true), 'title' => $this['option']->get('system:app.site_title'), 'type' => $this->getFeed()->getMIMEType()],
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

            $user = $this['user'];

            if (!$user->hasAccess('blog: post comments')) {
                throw new Exception(__('Insufficient User Rights.'));
            }

            // check minimum idle time in between user comments
            if (!$user->hasAccess('blog: skip comment min idle')
                and $minidle = $this->extension->getParams('comments.minidle')
                and $comment = $this->comments->query()->where($user->isAuthenticated() ? ['user_id' => $user->getId()] : ['ip' => $this['request']->getClientIp()])->orderBy('created', 'DESC')->first()) {

                $diff = $comment->getCreated()->diff(new \DateTime("- {$minidle} sec"));

                if ($diff->invert) {
                    throw new Exception(__('Please wait another %seconds% seconds before commenting again.', ['%seconds%' => $diff->s + $diff->i * 60 + $diff->h * 3600]));
                }
            }

            if (!$post = $this->posts->query()->where(['id' => $id, 'status' => Post::STATUS_PUBLISHED])->first()) {
                throw new Exception(__('Insufficient User Rights.'));
            }

            if (!$post->isCommentable()) {
                throw new Exception(__('Comments have been disabled for this post.'));
            }

            // retrieve user data
            if ($user->isAuthenticated()) {
                $data['author'] = $user->getName();
                $data['email'] = $user->getEmail();
                $data['url'] = $user->getUrl();
            } elseif ($this->extension->getParams('comments.require_name_and_email') && (!$data['author'] || !$data['email'])) {
                throw new Exception(__('Please provide valid name and email.'));
            }

            $comment = new Comment;
            $comment->setUserId((int) $user->getId());
            $comment->setIp($this['request']->getClientIp());
            $comment->setCreated(new \DateTime);
            $comment->setPost($post);

            $approved_once = (boolean) $this->comments->query()->where(['user_id' => $user->getId(), 'status' => Comment::STATUS_APPROVED])->first();
            $comment->setStatus($user->hasAccess('blog: skip comment approval') ? Comment::STATUS_APPROVED : $user->hasAccess('blog: comment approval required once') && $approved_once ? Comment::STATUS_APPROVED : Comment::STATUS_PENDING);

            // check the max links rule
            if ($comment->getStatus() == Comment::STATUS_APPROVED && $this->extension->getParams('comments.maxlinks') <= preg_match_all('/<a [^>]*href/i', @$data['content'])) {
                $comment->setStatus(Comment::STATUS_PENDING);
            }

            // check for spam
            $this['events']->dispatch('system.comment.spam_check', new CommentEvent($comment));

            $this->comments->save($comment, $data);

            $this['message']->info(__('Thanks for commenting!'));

            return $this->redirect($this['url']->route('@blog/id', ['id' => $post->getId()], true).'#comment-'.$comment->getId());

        } catch (Exception $e) {

            $this['message']->error($e->getMessage());

            return $this->redirect($this['url']->previous());

        } catch (\Exception $e) {

            $this['message']->error(__('Whoops, something went wrong!'));

            return $this->redirect($this['url']->previous());
        }
    }

    /**
     * @Route("/{id}", name="@blog/id")
     * @Response("extension://blog/views/post/post.razr")
     */
    public function postAction($id = 0)
    {
        if (!$post = $this->posts->where(['id = ?', 'status = ?', 'date < ?'], [$id, Post::STATUS_PUBLISHED, new \DateTime])->related('user')->first()) {
            throw new NotFoundHttpException(__('Post with id "%id%" not found!', ['%id%' => $id]));
        }

        if (!$post->hasAccess($this['user'])) {
            throw new AccessDeniedHttpException(__('Unable to access this post!'));
        }

        $user  = $this['user'];
        $query = $this->comments->query()->where(['status = ?'], [Comment::STATUS_APPROVED])->orderBy('created');

        if ($user->isAuthenticated()) {
            $query->orWhere(function($query) use ($user) {
                $query->where(['status = ?', 'user_id = ?'], [Comment::STATUS_PENDING, $user->getId()]);
            });
        }

        $this['db.em']->related($post, 'comments', $query);

        $post->setContent($this['content']->applyPlugins($post->getContent(), ['post' => $post, 'markdown' => $post->get('markdown')]));

        foreach ($post->getComments() as $comment) {
            $comment->setContent($this['content']->applyPlugins($comment->getContent(), ['comment' => true]));
        }

        return ['head.title' => __($post->getTitle()), 'post' => $post, 'params' => $this->extension->getParams()];
    }

    /**
     * @Route("/feed")
     * @Route("/feed/{type}")
     */
    public function feedAction($type = '')
    {
        $feed = $this->getFeed($type);

        $feed->setTitle($this['option']->get('system:app.site_title'));
        $feed->setLink($this['url']->route('@blog/site/index', [], true));
        $feed->setDescription($this['option']->get('system:app.site_description'));

        $feed->setChannelElement('language', $this['option']->get('system:app.locale'));

        if ($last = $this->posts->query()->where(['status = ?', 'date < ?'], [Post::STATUS_PUBLISHED, new \DateTime])->limit(1)->orderBy('modified', 'DESC')->first()) {
            $feed->setDate($last->getModified()->format(DATE_RSS));
        }

        $feed->setSelfLink($this['url']->route('@blog/site/feed', [], true));

        foreach ($this->posts->query()->where(['status = ?', 'date < ?'], [Post::STATUS_PUBLISHED, new \DateTime])->related('user')->limit($this->extension->getParams('feed.limit'))->orderBy('date', 'DESC')->get() as $post) {

            $item = $feed->createNewItem();

            $item->setTitle($post->getTitle());
            $item->setLink($this['url']->route('@blog/id', ['id' => $post->getId()], true));
            $item->setDescription($this['content']->applyPlugins($post->getContent(), ['post' => $post, 'markdown' => $post->get('markdown'), 'readmore' => true]));
            $item->setDate($post->getDate()->format(DATE_RSS));
            $item->setAuthor($post->getUser()->getName(), $post->getUser()->getEmail());
            $item->setId($this['url']->route('@blog/id', ['id' => $post->getId()], true), true);

            $feed->addItem($item);
        }

        return $this['response']->create($feed->generateFeed(), Response::HTTP_OK, array('Content-Type' => $feed->getMIMEType()));
    }

    /**
     * @param  string $type
     * @return Feed
     */
    protected function getFeed($type = '')
    {
        if (!$type) {
            $type = $this->extension->getParams('feed.type');
        }

        switch($type) {
            case 'atom':
                return new ATOM;
            case 'rss':
                return new RSS1;
            default:
                return new RSS2;
        }
    }
}
