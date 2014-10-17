<?php

namespace Pagekit\Blog\Event;

use Pagekit\Component\Database\ORM\Repository;
use Pagekit\Framework\Event\EventSubscriber;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class RouteListener extends EventSubscriber
{
    const CACHE_KEY = 'blog.routing';

    /**
     * @var bool
     */
    protected $cacheDirty = false;

    /**
     * @var array
     */
    protected $cacheEntries = [];

    /**
     * @var Repository
     */
    protected $posts;

    /**
     * @var string
     */
    protected $permalink;

    /**
     * Register alias routes
     */
    public function onSystemInit()
    {
        $extension = $this['extensions']->get('blog');

        if (!$this->permalink = $extension->getParams('permalink')) {
            return;
        }

        if ($this->permalink == 'custom') {
            $this->permalink = $extension->getParams('permalink.custom');
        }

        $this['router']->addAlias($this->permalink, '@blog/id', [$this, 'inbound'], [$this, 'outbound']);

        $this->cacheEntries = $this['cache']->fetch(self::CACHE_KEY);
    }

    public function inbound($parameters)
    {
        $parameters['id'] = $this->getId($parameters['slug']);
        return $parameters;
    }

    public function outbound($parameters)
    {
        $meta = $this->getMeta($parameters['id']);

        preg_match_all('#{([a-z]+)}#i', $this->permalink, $matches);

        if ($matches) {
            foreach($matches[1] as $attribute) {
                if (isset($meta[$attribute])) {
                    $parameters[$attribute] = $meta[$attribute];
                }
            }
        }

        unset($parameters['id']);
        return $parameters;
    }

    protected function getMeta($id)
    {
        if (!isset($this->cacheEntries[$id])) {

            if (!$post = $this->getPosts()->where(compact('id'))->first()) {
                throw new RouteNotFoundException(__('Post with id "%id%" not found!', ['%id%' => $id]));
            }

            $this->cacheEntries[$id] = [
                'id'     => $post->getId(),
                'slug'   => $post->getSlug(),
                'year'   => $post->getDate()->format('Y'),
                'month'  => $post->getDate()->format('m'),
                'day'    => $post->getDate()->format('d'),
                'hour'   => $post->getDate()->format('H'),
                'minute' => $post->getDate()->format('i'),
                'second' => $post->getDate()->format('s'),
            ];

            $this->cacheDirty = true;
        }

        return $this->cacheEntries[$id];
    }

    protected function getId($slug)
    {
        if (!isset($this->cacheEntries[$slug])) {

            if (!$post = $this->getPosts()->where(compact('slug'))->first()) {
                throw new NotFoundHttpException(__('Post with slug "%slug%" not found!', ['%slug%' => $slug]));
            }

            $this->cacheEntries[$slug] = $post->getId();
            $this->cacheDirty = true;
        }

        return $this->cacheEntries[$slug];
    }

    public function clearCache()
    {
        $this->cacheEntries = [];
        $this->cacheDirty = true;
    }

    public function __destruct()
    {
        if ($this->cacheDirty) {
            $this['cache']->save(self::CACHE_KEY, $this->cacheEntries);
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'system.init'          => 'onSystemInit',
            'blog.post.postSave'   => 'clearCache',
            'blog.post.postDelete' => 'clearCache'
        ];
    }

    /**
     * @return Repository
     */
    protected function getPosts()
    {
        if (!$this->posts) {
            $this->posts = $this['db.em']->getRepository('Pagekit\Blog\Entity\Post');
        }

        return $this->posts;
    }
}