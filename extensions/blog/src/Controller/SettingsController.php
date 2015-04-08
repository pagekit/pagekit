<?php

namespace Pagekit\Blog\Controller;

use Pagekit\Application as App;
use Pagekit\Application\Controller;

/**
 * @Access("blog: manage settings", admin=true)
 */
class SettingsController extends Controller
{
    /**
     * @Response("blog:views/admin/settings.php")
     */
    public function indexAction()
    {
        return [
            '$meta' => [
                'title' => __('Blog Settings')
            ],
            '$data' => [
                'config' => App::module('blog')->config()
            ]
        ];
    }
}
