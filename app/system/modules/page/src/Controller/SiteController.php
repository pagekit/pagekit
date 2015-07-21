<?php

namespace Pagekit\Page\Controller;

use Pagekit\Application as App;
use Pagekit\Page\Model\Page;

class SiteController
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
                'name'  => 'system/page:views/index.php'
            ],
            'page' => $page
        ];
    }
}
