<?php

namespace Pagekit\Site\Controller;

use Pagekit\Application as App;
use Pagekit\Site\Model\Page;

class PageController
{
    public function indexAction($id = 0)
    {
        if (!$page = Page::find($id)) {
            App::abort(404, __('Page not found.'));
        }

        if (!App::node()->hasAccess(App::user())) {
            App::abort(403, __('Insufficient User Rights.'));
        }

        $page->setContent(App::content()->applyPlugins($page->getContent(), ['page' => $page, 'markdown' => $page->get('markdown')]));

        return [
            '$view' => [
                'title' => __($page->getTitle()),
                'name'  => 'system/site:views/page.php'
            ],
            'page' => $page,
            'node' => App::node()
        ];
    }
}
