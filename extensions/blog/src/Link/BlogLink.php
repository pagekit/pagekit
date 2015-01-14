<?php

namespace Pagekit\Blog\Link;

use Pagekit\Blog\Entity\Post;
use Pagekit\Framework\Application as App;
use Pagekit\System\Link\LinkInterface;

class BlogLink implements LinkInterface
{
    /**
     * @{inheritdoc}
     */
    public function getId()
    {
        return 'blog';
    }

    /**
     * @{inheritdoc}
     */
    public function getLabel()
    {
        return __('Blog');
    }

    /**
     * @{inheritdoc}
     */
    public function accept($route)
    {
        return $route == '@blog/site' || $route == '@blog/id';
    }

    /**
     * @{inheritdoc}
     */
    public function renderForm($link, $params = [], $context = '')
    {
        $posts = Post::findAll();

        return App::view()->render('extensions/blog/views/admin/link/blog.razr', compact('link', 'params', 'posts'));
    }
}
