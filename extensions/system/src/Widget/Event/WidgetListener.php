<?php

namespace Pagekit\Widget\Event;

use Pagekit\Framework\Event\EventSubscriber;
use Pagekit\Widget\Entity\Widget;

class WidgetListener extends EventSubscriber
{
    /**
     * Handles the widget to position assignment.
     */
    public function onSiteInit()
    {
        $active    = (array) $this('request')->attributes->get('_menu');
        $path      = ltrim($this('request')->getPathInfo(), '/');
        $positions = $this('positions');

        foreach ($this('widgets')->getWidgetRepository()->where('status = ?', array(Widget::STATUS_ENABLED))->orderBy('priority')->get() as $widget) {

            // filter by access
            if (!$this('users')->checkAccessLevel($widget->getAccessId())) {
                continue;
            }

            // filter by menu items and pages
            $items = $widget->getMenuItems();
            $pages = $widget->getPages();

            $itemsMatch = (bool) array_intersect($items, $active);
            $pagesMatch = $this->matchPath($path, $pages);

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
            'site.init' => array('onSiteInit', -16)
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

        foreach (explode("\n", str_replace(array('\!', '\*', "\r"), array('!', '.*', ''), preg_quote($patterns, '/'))) as $pattern) {

            if (empty($pattern)) {
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
