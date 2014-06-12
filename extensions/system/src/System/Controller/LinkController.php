<?php

namespace Pagekit\System\Controller;

use Pagekit\Framework\Controller\Controller;
use Pagekit\System\Event\LinkEvent;

/**
 * @Access(admin=true)
 */
class LinkController extends Controller
{
    /**
     * @Request({"context"})
     * @View("system/admin/links/link.types.razr.php", layout=false)
     */
    public function indexAction($context = '')
    {
        return array('links' => $this('events')->dispatch('system.link', new LinkEvent($context)));
    }
}
