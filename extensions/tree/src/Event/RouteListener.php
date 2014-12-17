<?php

namespace Pagekit\Tree\Event;

use Pagekit\Framework\Event\EventSubscriber;

class RouteListener extends EventSubscriber
{
    /**
     * Adds page aliases.
     */
    public function onSystemInit()
    {
        foreach ($this['option']->get('tree.pages') ?: $this->cache() as $page) {

            if ($page['url']) {

                $this['aliases']->add($page['path'], $page['url']);

            } elseif ($page['mount'] and isset($this['mounts'][$page['mount']])) {

                $this['controllers']->mount($page['path'], $this['mounts'][$page['mount']]['controller'], "@{$page['mount']}/");

            }
        }
    }

    /**
     * @return array
     */
    public function cache()
    {
        $pages = [];
        foreach ($this['db.em']->getRepository('Pagekit\Tree\Entity\Page')->query()->where(['status = ?'], [1])->get() as $page) {
            $pages[] = ['path' => $page->getPath(), 'url' => $page->getUrl(), 'mount' => $page->getMount()];
        }

        $this['option']->set('tree.pages', $pages, true);

        return $pages;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'system.init' => ['onSystemInit', 10],
            'tree.page.postSave'   => 'cache',
            'tree.page.postDelete' => 'cache'
        ];
    }
}
