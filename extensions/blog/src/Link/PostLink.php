<?php

namespace Pagekit\Blog\Link;

use Pagekit\System\Link\Route;

class PostLink extends Route
{
    /**
     * @{inheritdoc}
     */
    public function getRoute()
    {
        return '@blog/id';
    }

    /**
     * @{inheritdoc}
     */
    public function getLabel()
    {
        return __('Blog Post');
    }

    /**
     * @{inheritdoc}
     */
    public function renderForm($link, $params = [])
    {
        $posts = $this('db.em')->getRepository('Pagekit\Blog\Entity\Post')->findAll();

        return $this('view')->render('blog/admin/link/post.razr', compact('link', 'params', 'posts'));
    }
}
