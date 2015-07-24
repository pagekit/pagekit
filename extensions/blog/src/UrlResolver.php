<?php

namespace Pagekit\Blog;

use Pagekit\Application as App;
use Pagekit\Blog\Model\Post;
use Pagekit\Routing\ParamsResolverInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class UrlResolver implements ParamsResolverInterface
{
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
        $this->permalink    = App::module('blog')->getPermalink();
        $this->cacheEntries = App::cache()->fetch(self::CACHE_KEY) ?: [];
    }

    /**
     * {@inheritdoc}
     */
    public function match(array $parameters = [])
    {
        if (isset($parameters['id'])) {
            return $parameters;
        }

        if (!isset($parameters['slug'])) {
            App::abort(404, 'Post not found.');
        }

        $slug = $parameters['slug'];

        $id = false;
        foreach ($this->cacheEntries as $entry) {
            if ($entry['slug'] === $slug) {
                $id = $entry['id'];
            }
        }

        if (!$id) {

            if (!$post = Post::where(compact('slug'))->first()) {
                App::abort(404, 'Post not found.');
            }

            $this->addCache($post);
            $id = $post->getId();
        }

        $parameters['id'] = $id;
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
                throw new RouteNotFoundException('Post not found!');
            }

            $this->addCache($post);
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
            App::cache()->save(self::CACHE_KEY, $this->cacheEntries);
        }
    }

    protected function addCache($post)
    {
        $this->cacheEntries[$post->getId()] = [
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
}
