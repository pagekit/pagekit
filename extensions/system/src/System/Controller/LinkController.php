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
     * @Request({"url"})
     */
    public function resolveUrlAction($url)
    {
        $url = $this('system.info')->resolveURL($url);

        $url = $url !== '' ? $url : __('Frontpage');

        return $this('response')->json(false !== $url ? compact('url') : array('error' => true, 'message' => __('Invalid URL.')));
    }
}
