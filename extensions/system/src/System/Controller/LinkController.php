<?php

namespace Pagekit\System\Controller;

use Pagekit\Component\Routing\Link;
use Pagekit\Framework\Controller\Controller;
use Pagekit\System\Event\LinkEvent;
use Pagekit\System\Link\LinkInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

/**
 * @Access(admin=true)
 */
class LinkController extends Controller
{
    /**
     * @var LinkEvent
     */
    protected $types;

    /**
     * @Request({"context"})
     * @View("system/admin/links/link.types.razr", layout=false)
     */
    public function indexAction($context = '')
    {
        return array('links' => $this->getTypes($context));
    }

    /**
     * @Request({"link", "context"})
     */
    public function formAction($url, $context = '')
    {
        $result = null;

        if ($type = $this->matchType($url, $context)) {

            $link = $this->getLink($url);

            $params = [];
            if ($query = substr(strstr($link, '?'), 1)) {
                parse_str($query, $params);
                $link = strstr($link, '?', true);
            }

            $result = array('type' => $type->getId(), 'form' => $type->renderForm($link, $params));
        }

        return $this['response']->json($result);
    }

    /**
     * @Request({"link", "context"})
     */
    public function resolveAction($url, $context = '')
    {
        $result = ['type' => __('Url'), 'url' => $url];

        if ($type = $this->matchType($url, $context)) {
            $result = array('type' => $type->getLabel(), 'url' => urldecode($this['url']->route($url, [], 'base')));
        }

        return $this['response']->json($result);
    }

    /**
     * @param  string $context
     * @return LinkEvent
     */
    protected function getTypes($context = '')
    {
        if (null == $this->types) {
            $this->types = $this['events']->dispatch('system.link', new LinkEvent($context));
        }

        return $this->types;
    }

    /**
     * @param  string $url
     * @param  string $context
     * @return LinkInterface|false
     */
    protected function matchType($url, $context = '')
    {
        $types = $this->getTypes($context);

        if (isset($types[$url])) {
            return $types[$url];
        }

        $link = strtok(strtok($this->getLink($url), '?'), '#');

        foreach ($types as $type) {

            if ($type->accept($link ?: $url)) {
                return $type;
            }

        }

        return false;
    }

    /**
     * @param  $url
     * @return string|false
     */
    protected function getLink($url)
    {
        try {

            return $this['router']->generate($url, [], 'link');

        } catch (RouteNotFoundException $e) {}

        return false;
    }
}