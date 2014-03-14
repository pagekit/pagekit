<?php

namespace Pagekit\System\Event;

use Pagekit\Framework\Event\EventSubscriber;

class AliasListener extends EventSubscriber
{
    const CACHE_KEY = 'system:url_aliases';

    /**
     * Handles alias mapping.
     */
    public function onBoot()
    {
        $manager = $this('router')->getUrlAliases();

        if (false === $aliases = $this('cache')->fetch(self::CACHE_KEY)) {
            $aliases = $this('db.em')->getRepository('Pagekit\System\Entity\Alias')->findAll();
            $this('cache')->save(self::CACHE_KEY, $aliases);
        }

        foreach ($aliases as $alias) {
            $manager->register($alias->getAlias(), $this('url')->route($alias->getSource()));
        }
    }

    /**
     * Clear cache on entity save.
     */
    public function clearCache()
    {
        $this('cache')->delete(self::CACHE_KEY);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            'boot'                    => array('onBoot', 8),
            'system.alias.postSave'   => 'clearCache',
            'system.alias.postDelete' => 'clearCache'
        );
    }
}
