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
    public function onSystemInit()
    {
        $router = $this['router'];
        $aliases = $this['cache.phpfile']->fetch('page.aliases') ?: array();

        if (!$aliases) {

            $urls = $this['db']->createQueryBuilder()
                ->from('@page_page')
                ->where('url <> ""')
                ->execute('id, url')
                ->fetchAll(\PDO::FETCH_KEY_PAIR);

            foreach ($urls as $id => $url) {
                $aliases[$url] = '@page/id?id=' . $id;
            }

            $this['cache.phpfile']->save(self::CACHE_KEY, $aliases);
        }

        foreach ($aliases as $alias => $source) {
            $router->addAlias($alias, $source);
        }
    }

    /**
     * Clears the url aliases cache.
     */
    public function clearCache()
    {
        $this['cache.phpfile']->delete(self::CACHE_KEY);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            'system.init'          => 'onSystemInit',
            'page.page.postSave'   => 'clearCache',
            'page.page.postDelete' => 'clearCache'
        );
    }
}
