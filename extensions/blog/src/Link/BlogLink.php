<?php

namespace Pagekit\Blog\Link;

use Pagekit\Blog\Entity\Post;
use Pagekit\System\Link\Link;

class BlogLink extends Link
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

        return $this['view']->render('extensions/blog/views/admin/link/blog.razr', compact('link', 'params', 'posts'));
    }
}
