<?php

namespace Pagekit\Page\Event;

use Pagekit\Framework\Event\EventSubscriber;

class AliasListener extends EventSubscriber
{
    const CACHE_KEY = 'page.aliases';

    /**
     * Registers the url aliases.
     */
    public function onSystemInit()
    {
        $router = $this['router'];
        $aliases = $this['cache.phpfile']->fetch('page.aliases') ?: [];

        if (!$aliases) {

            $aliases = array_map(function($id) {
                    return '@page/id?id='.$id;
                },
                $this['db']->createQueryBuilder()
                    ->from('@page_page')
                    ->where('url <> ""')
                    ->execute('url, id')
                    ->fetchAll(\PDO::FETCH_KEY_PAIR)
            );

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
        return [
            'system.init'          => 'onSystemInit',
            'page.page.postSave'   => 'clearCache',
            'page.page.postDelete' => 'clearCache'
        ];
    }
}
