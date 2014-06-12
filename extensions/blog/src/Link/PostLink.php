<?php

namespace Pagekit\Blog\Link;

use Pagekit\System\Link\Link;

class PostLink extends Link
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
    public function renderForm()
    {
        $posts = $this('db.em')->getRepository('Pagekit\Blog\Entity\Post')->findAll();

        return $this('view')->render('blog/admin/link/post.razr.php', array('route' => $this->getRoute(), 'posts' => $posts));
    }
}
