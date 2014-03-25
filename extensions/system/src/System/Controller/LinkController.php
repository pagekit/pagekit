<?php

namespace Pagekit\System\Controller;

use Pagekit\Framework\Controller\Controller;
use Pagekit\System\Event\RegisterLinkEvent;

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
        $this('events')->dispatch('link.register', $event = new RegisterLinkEvent($context));
        return array('links' => $event);
    }

    /**
     * @Request({"link", "context"})
     */
    public function resolveAction($link, $context = '')
    {
        $this('events')->dispatch('link.register', $event = new RegisterLinkEvent($context));

        $resolved = false;
        foreach ($event as $type) {
            if ($link == $type->getRoute()) {
                $resolved = $type->getLabel();
            }
        }

        $resolved = $resolved ?: $this('system.info')->resolveURL($link);

        return $this('response')->json(false !== $resolved ? array('url' => $resolved) : array('error' => true, 'message' => __('Invalid URL.')));
    }
}
