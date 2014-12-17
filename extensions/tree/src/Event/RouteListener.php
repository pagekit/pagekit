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
        $pages = $this['db.em']->getRepository('Pagekit\Tree\Entity\Page')->query()->where(['status = ?'], [1])->get();

        foreach ($pages as $page) {

            if ($page->getUrl()) {

                $this['aliases']->add($page->getPath(), $page->getUrl());

            } elseif ($mount = $page->getMount() and isset($this['mounts'][$mount])) {

                $this['controllers']->mount($page->getPath(), $this['mounts'][$mount]['controller'], "@{$mount}/");

            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'system.init' => ['onSystemInit', 10]
        ];
    }
}
