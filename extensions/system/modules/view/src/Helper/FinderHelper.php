<?php

namespace Pagekit\View\Helper;

use Pagekit\Application as App;

class FinderHelper
{
    /**
     * Renders the finder.
     *
     * @param  string $root
     * @return string
     */
    public function __invoke($root)
    {
        App::scripts('finder.init', 'require(["system!finder", "domReady!"], function(system) { $("[data-finder]").each(function() { system.finder(this, $(this).data("finder")).loadPath(); }); });', 'requirejs', 'string');

        return "<div data-finder='".json_encode(compact('root'))."'></div>";
    }
}
