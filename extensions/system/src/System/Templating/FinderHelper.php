<?php

namespace Pagekit\System\Templating;

use Pagekit\Framework\Application;
use Symfony\Component\Templating\Helper\Helper;

class FinderHelper extends Helper
{
    /**
     * @var Application
     */
    protected $app;

    function __construct(Application $app)
    {
        $this->app = $app;
    }

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
        $this->app['view.scripts']->queue('finder.init', 'require(["system!finder", "domReady!"], function(system) { $("[data-finder]").each(function() { system.finder(this, $(this).data("finder")).loadPath(); }); });', 'requirejs', 'string');

        return "<div data-finder='".json_encode(compact('root'))."'></div>";
    }
}
