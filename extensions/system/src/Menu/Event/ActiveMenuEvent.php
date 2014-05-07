<?php

namespace Pagekit\Menu\Event;

use Pagekit\Framework\Event\Event;

class ActiveMenuEvent extends Event
{
    /**
     * @var int[]
     */
    protected $active = array();

    /**
     * @var array
     */
    protected $items = array('paths' => array(), 'patterns' => array());

    /**
     * @param array $items
     */
    public function __construct(array $items)
    {
        $this->items = $items + $this->items;
    }

    /**
     * Gets items for a route/path
     *
     * @param  array $path
     * @return array
     */
    public function get($path)
    {
        $items = array();
        foreach((array) $path as $p) {
            if (isset($this->items['paths'][$p])) {
                $items += $this->items['paths'][$p];
            }
        }
        return $items;
    }

    /**
     * Add items whose pattern match the given path.
     *
     * @param $path
     */
    public function match($path)
    {
        foreach ($this->items['patterns'] as $id => $pattern) {
            if ($this->matchPath($path, $pattern)) {
                $this->add($id);
            }
        }
    }

    /**
     * Add active menu item(s)
     *
     * @param string|array $active
     */
    public function add($active)
    {
        $this->active = array_merge($this->active, (array) $active);
    }

    /**
     * Get active menu item ids
     *
     * @return int[]
     */
    public function getActive()
    {
        return $this->active;
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