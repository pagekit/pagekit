<?php

namespace Pagekit\Blog\Controller;

use Pagekit\Blog\BlogExtension;
use Pagekit\Blog\Entity\Comment;
use Pagekit\Blog\Entity\Post;
use Pagekit\Comment\Event\CommentEvent;
use Pagekit\Component\Database\ORM\Repository;
use Pagekit\Framework\Controller\Controller;
use Pagekit\Framework\Controller\Exception;

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
    }

    /**
     * @Response("blog/post/index.razr")
     */
    public function indexAction()
    {
        $posts = $this->posts->query()->where(['status = ?', 'date < ?'], [Post::STATUS_PUBLISHED, new \DateTime])->related('user')->orderBy('date', 'DESC')->get();

        foreach ($posts as $post) {
            $post->setContent($this['content']->applyPlugins($post->getContent(), ['post' => $post, 'markdown' => $post->get('markdown'), 'readmore' => true]));
        }

        return ['head.title' => __('Blog'), 'posts' => $posts, 'config' => $this->extension->getConfig()];
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
                and $minidle = $this->extension->getConfig('comments.minidle')
                and $comment = $this->comments->query()->where($user->isAuthenticated() ? ['user_id' => $user->getId()] : ['ip' => $this['request']->getClientIp()])->orderBy('created', 'DESC')->first()) {

                $diff = $comment->getCreated()->diff(new \DateTime("- {$minidle} sec"));

                if ($diff->invert) {
                    throw new Exception(__('Please wait another %seconds% seconds before commenting again.', ['%seconds%' => $diff->s+$diff->i*60+$diff->h*3600]));
                }
            }

            if (!$post = $this->posts->query()->where(['id' => $id, 'status' => Post::STATUS_PUBLISHED])->first()) {
                throw new Exception(__('Insufficient User Rights.'));
            }

            if (!$post->getCommentStatus()) {
                throw new Exception(__('Comments have been disabled for this post.'));
            }

            // retrieve user data
            if ($user->isAuthenticated()) {
                $data['author'] = $user->getName();
                $data['email'] = $user->getEmail();
                $data['url'] = $user->getUrl();
            } elseif ($this->extension->getConfig('comments.require_name_and_email') && (!$data['author'] || !$data['email'])) {
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
            if ($comment->getStatus() == Comment::STATUS_APPROVED && $this->extension->getConfig('comments.maxlinks') <= preg_match_all('/<a [^>]*href/i', @$data['content'])) {
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
     * @Response("blog/post/post.razr")
     */
    public function postAction($id = 0)
    {
        if (!$post = $this->posts->where(['id = ?', 'status = ?', 'date < ?'], [$id, Post::STATUS_PUBLISHED, new \DateTime])->first()) {
            return $this['response']->create(__('Post not found!'), 404);
        }

        if (!$post->hasAccess($this['user'])) {
            return $this['response']->create(__('Unable to access this post!'), 403);
        }

        $user  = $this['user'];
        $query = $this->comments->query()->where(['status = ?'], [Comment::STATUS_APPROVED]);

        if ($user->isAuthenticated()) {
            $query->orWhere(function($query) use ($user) {
                $query->where(['status = ?', 'user_id = ?'], [Comment::STATUS_PENDING, $user->getId()]);
            });
        }

        $this['db.em']->related($post, 'comments', $query);

        if ($post->getCommentStatus() && $this->extension->getConfig('comments.autoclose')) {
            $days = $this->extension->getConfig('comments.autoclose.days', 0);
            if ($days && $post->getDate() < new \DateTime("-{$days} day")) {
                $post->setCommentStatus(false);
                $this->posts->save($post);
            }
        }

        $post->setContent($this['content']->applyPlugins($post->getContent(), ['post' => $post, 'markdown' => $post->get('markdown')]));

        foreach ($post->getComments() as $comment) {
            $comment->setContent($this['content']->applyPlugins($comment->getContent(), ['comment' => true]));
        }

        return ['head.title' => __($post->getTitle()), 'post' => $post, 'config' => $this->extension->getConfig()];
    }
}
