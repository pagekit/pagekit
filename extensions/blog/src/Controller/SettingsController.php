<?php

namespace Pagekit\Blog\Controller;

use Pagekit\Application as App;

/**
 * @Access("blog: manage settings", admin=true)
 */
class SettingsController
{
    public function indexAction()
    {
        return [
            '$view' => [
                'title' => __('Blog Settings'),
                'name'  => 'blog:views/admin/settings.php'
            ],
            '$data' => [
                'config' => App::module('blog')->config()
            ]
        ];
    }
}
