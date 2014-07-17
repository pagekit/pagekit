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
     * @Response("extension://system/views/admin/link/link.types.razr", layout=false)
     */
    public function indexAction($context = '')
    {
        return ['links' => $this->getTypes($context)];
    }

    /**
     * @Request({"link", "context"})
     * @Response("json")
     */
    public function formAction($url, $context = '')
    {
        if ($type = $this->matchType($url, $context)) {

            $params = [];
            $link = $this->getLink($url);

            if ($query = substr(strstr($link, '?'), 1)) {
                parse_str($query, $params);
                $link = strstr($link, '?', true);
            }

            return ['type' => $type->getId(), 'form' => $type->renderForm($link ?: $url, $params, $context)];
        }
    }

    /**
     * @Request({"link", "context"})
     * @Response("json")
     */
    public function resolveAction($url, $context = '')
    {
        $result = ['type' => __('Url'), 'url' => $url];

        if ($type = $this->matchType($url, $context)) {
            try {

                if (!in_array($context, ['frontpage', 'urlalias'])) {
                    $url = urldecode($this['url']->to($url, [], 'base'));
                }

                $result = ['type' => $type->getLabel(), 'url' => $url];

            } catch (\Exception $e) {}
        }

        return $result;
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

        $url = strtok($url, '#');

        if (isset($types[$url])) {
            return $types[$url];
        }

        $link = strtok($this->getLink($url), '?');

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

            $url = strtok($url, '#');

            return $this['router']->generate($url, [], 'link');

        } catch (RouteNotFoundException $e) {}

        return false;
    }
}