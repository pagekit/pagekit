<?php

namespace Pagekit\Page\Controller;

use Pagekit\Application as App;
use Pagekit\Page\Entity\Page;
use Pagekit\Kernel\Exception\NotFoundException;

class SiteController
{
    /**
     * @Route("/{id}", name="id")
     * @Request({"id" : "int"})
     * @Response("page:views/index.php")
     */
    public function indexAction($id = 0)
    {
        if (!$page = Page::find($id)) {
            throw new NotFoundException(__('Page not found!'));
        }

        $page->setContent(App::content()->applyPlugins($page->getContent(), ['page' => $page, 'markdown' => $page->get('markdown')]));

        return [
            '$meta' => [
                'title' => __($page->getTitle())
            ],
            'page' => $page
        ];
    }
}
