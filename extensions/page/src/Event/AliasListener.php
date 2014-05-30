<?php

namespace Pagekit\Page\Event;

use Pagekit\Framework\Event\EventSubscriber;
use Pagekit\Page\Entity\Page;

class AliasListener extends EventSubscriber
{
    const CACHE_KEY = 'page.aliases';

    /**
     * Registers the url aliases.
     */
    public function registerAliases()
    {
        $manager = $this('router')->getUrlAliases();
        $aliases = $this('cache.phpfile')->fetch('page.aliases') ?: array();

        if (!$aliases) {

            $pages = $this('db.em')->getRepository('Pagekit\Page\Entity\Page');

            foreach ($pages->where(array('status' => Page::STATUS_PUBLISHED))->get() as $page) {
                if ($page->getUrl() !== '') {
                    $aliases[$page->getUrl()] = $this('url')->route('@page/id', array('id' => $page->getId()), 'base');
                }
            }

            $this('cache.phpfile')->save(self::CACHE_KEY, $aliases);
        }

        foreach ($aliases as $alias => $source) {
            $manager->register($alias, $source);
        }
    }

    /**
     * Clears the url aliases cache.
     */
    public function clearCache()
    {
        $this('cache.phpfile')->delete(self::CACHE_KEY);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            'system.init'          => 'registerAliases',
            'page.page.postSave'   => 'clearCache',
            'page.page.postDelete' => 'clearCache'
        );
    }
}
