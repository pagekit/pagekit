<?php

namespace Pagekit\Templating\Helper;

use Pagekit\Application as App;
use Symfony\Component\Templating\Helper\Helper;

class FinderHelper extends Helper
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'finder';
    }

    /**
     * @param  string $root
     * @return string
     */
    public function render($root)
    {
        App::scripts('finder.init', 'require(["system!finder", "domReady!"], function(system) { $("[data-finder]").each(function() { system.finder(this, $(this).data("finder")).loadPath(); }); });', 'requirejs', 'string');

        return "<div data-finder='".json_encode(compact('root'))."'></div>";
    }
}
