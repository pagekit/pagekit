<?php

namespace Pagekit\Widget\Event;

use Pagekit\Framework\Event\EventSubscriber;
use Pagekit\Widget\Entity\Widget;

class WidgetListener extends EventSubscriber
{
    /**
     * Handles the widget to position assignment.
     */
    public function onSiteLoaded()
    {
        $request   = $this('request');
        $positions = $this('positions');
        $active    = (array) $request->attributes->get('_menu');

        foreach ($this('widgets')->getWidgetRepository()->where('status = ?', array(Widget::STATUS_ENABLED))->orderBy('priority')->get() as $widget) {

            // filter by access
            if (!$this('users')->checkAccessLevel($widget->getAccessId())) {
                continue;
            }

            // filter by menu items and pages
            $items = $widget->getMenuItems();
            $pages = $widget->getPages();

            $itemsMatch = (bool) array_intersect($items, $active);
            $pagesMatch = $this->matchPath($request->getPathInfo(), $pages);

            if (!( ((!$items || $itemsMatch) && (!$pages || $pagesMatch)) || ($items && $itemsMatch) || ($pages && $pagesMatch) )) {
                continue;
            }

            $positions[$widget->getPosition()][$widget->getId()] = $widget;
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            'site.loaded' => array('onSiteLoaded', -16)
        );
    }

    /**
     * @param  string $path
     * @param  string $patterns
     * @return bool
     */
    protected function matchPath($path, $patterns)
    {
        $negatives = $positives = '';

        $patterns = preg_replace('/^(\!)?([^\!\/])/m', '$1/$2', $patterns);
        $patterns = preg_quote($patterns, '/');

        foreach (explode("\n", str_replace(array('\!', '\*', "\r"), array('!', '.*', ''), $patterns)) as $pattern) {
            if ($pattern === '') {
                continue;
            } elseif ($pattern[0] === '!') {
                $negatives .= ($negatives ? '|' : '').$pattern;
            } else {
                $positives .= ($positives ? '|' : '').$pattern;
            }
        }

        return (bool) preg_match('/^'.($negatives ? '(?!('.str_replace('!', '', $negatives).')$)' : '').($positives ? '('.$positives.')' : '.*').'$/', $path);
    }
}
