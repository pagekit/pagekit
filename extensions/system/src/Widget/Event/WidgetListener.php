<?php

namespace Pagekit\Widget\Event;

use Pagekit\Framework\Event\EventSubscriber;
use Pagekit\Widget\Entity\Widget;
use Pagekit\Widget\Model\TypesTrait;

class WidgetListener extends EventSubscriber
{
    /**
     * Handles the widget to position assignment.
     */
    public function onSystemSite()
    {
        $request   = $this['request'];
        $active    = (array) $request->attributes->get('_menu');
        $user      = $this['user'];
        $sections  = $this['view.sections'];

        foreach ($this['db.em']->getRepository('Pagekit\Widget\Entity\Widget')->where('status = ?', [Widget::STATUS_ENABLED])->orderBy('priority')->get() as $widget) {

            // filter by access
            if (!$widget->hasAccess($user)) {
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

            $sections->append($widget->getPosition(), $widget);
        }
    }

    /**
     * Registers widget types.
     */
    public function onSystemLoaded()
    {
        TypesTrait::setWidgetTypes($this['events']->dispatch('system.widget', new RegisterWidgetEvent)->getTypes());
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'system.loaded' => ['onSystemLoaded', 5],
            'system.site'   => ['onSystemSite', -16]
        ];
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

        foreach (explode("\n", str_replace(['\!', '\*', "\r"], ['!', '.*', ''], $patterns)) as $pattern) {
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
