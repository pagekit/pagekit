<?php

namespace Pagekit\Finder;

use Pagekit\Application as App;

class FinderHelper
{
    /**
     * @param  string $root
     * @param  string $mode
     * @return string
     */
    public function __invoke($root, $mode = 'r')
    {
        App::scripts('finder');

        return "<div data-finder='".json_encode(compact('root', 'mode'))."'></div>";
    }
}
