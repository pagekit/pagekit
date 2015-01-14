<?php

namespace Pagekit\Blog;

use Pagekit\Blog\Entity\Post;
use Pagekit\Component\Routing\ParamsResolverInterface;
use Pagekit\Framework\ApplicationTrait;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class UrlResolver implements ParamsResolverInterface, \ArrayAccess
{
    use ApplicationTrait;

    const CACHE_KEY = 'blog.routing';

    /**
     * @var bool
     */
    protected $cacheDirty = false;

    /**
     * @var array
     */
    protected $cacheEntries;

    /**
     * @var string
     */
    protected $permalink;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $extension = $this['extensions']->get('blog');

        $this->permalink = $extension->getParams('permalink');

        if ($this->permalink == 'custom') {
            $this->permalink = $extension->getParams('permalink.custom');
        }

        $this->cacheEntries = $this['cache']->fetch(self::CACHE_KEY) ?: [];
    }

    /**
     * {@inheritdoc}
     */
    public function match(array $parameters = [])
    {
        $slug = $parameters['slug'];

        if (!isset($this->cacheEntries[$slug])) {

            if (!$post = Post::where(compact('slug'))->first()) {
                throw new NotFoundHttpException(__('Post with slug "%slug%" not found!', ['%slug%' => $slug]));
            }

            $this->cacheEntries[$slug] = $post->getId();
            $this->cacheDirty = true;
        }

        $parameters['id'] = $this->cacheEntries[$slug];
        return $parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(array $parameters = [])
    {
        $id = $parameters['id'];

        if (!isset($this->cacheEntries[$id])) {

            if (!$post = Post::where(compact('id'))->first()) {
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

        $meta = $this->cacheEntries[$id];

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

    public function __destruct()
    {
        if ($this->cacheDirty) {
            $this['cache']->save(self::CACHE_KEY, $this->cacheEntries);
        }
    }
}
