<?php

namespace Pagekit\Page;

use Pagekit\Application as App;
use Pagekit\Module\Module;
use Pagekit\Page\Model\Page;
use Pagekit\Site\Model\Node;

class PageModule extends Module
{
    // TODO: move to SiteListener

    /**
     * Find page entity by node.
     *
     * @param  Node $node
     * @return Page
     */
    public function getPage(Node $node)
    {
        $defaults = $node->get('defaults', []);

        if (!isset($defaults['id']) or !$page = Page::find($defaults['id'])) {
            $page = new Page();
        }

        return $page;
    }
}
