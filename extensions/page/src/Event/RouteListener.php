<?php

namespace Pagekit\Page\Event;

use Pagekit\Component\Database\ORM\Repository;
use Pagekit\Component\Routing\Event\GenerateRouteEvent;
use Pagekit\Framework\Event\EventSubscriber;

class RouteListener extends EventSubscriber
{
    const CACHE_KEY = 'page:metadata.';

    /**
     * @var Repository
     */
    protected $pages;

    /**
     * Translate page routes
     *
     * @param GenerateRouteEvent $event The event to handle
     * @throws \RuntimeException
     */
    public function onGenerateRoute(GenerateRouteEvent $event)
    {
        if ($event->getRoute() !== '@page/id') {
            return;
        }

        $params = $event->getParameters();
        $id     = $params['id'];

        if (!$meta = $this('cache')->fetch(self::CACHE_KEY.$id)) {
            if (!$page = $this->getPages()->find($id)) {
                throw new \RuntimeException(__('Page with id "%id%" not found!', array('%id%' => $id)));
            }

            $this('cache')->save(self::CACHE_KEY.$id, $meta = array('slug' => $page->getSlug()));
        }

        unset($params['id']);
        $params['slug'] = $meta['slug'];
        $event->setRoute('@page/slug');
        $event->setParameters($params);
    }

    public function clearCache()
    {
        $this('cache')->flushAll();
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            'route.generate'       => 'onGenerateRoute',
            'page.page.postSave'   => 'clearCache',
            'page.page.postDelete' => 'clearCache'
        );
    }

    /**
     * @return Repository
     */
    protected function getPages()
    {
        if (!$this->pages) {
            $this->pages = $this('db.em')->getRepository('Pagekit\Page\Entity\Page');
        }

        return $this->pages;
    }
}