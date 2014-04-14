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
     * @param  string $mode
     * @return string
     */
    public function render($root, $mode)
    {
        $this->app['view.scripts']->queue('finder.init', 'require(["finder", "domReady!"], function(Finder) { $("[data-finder]").each(function() { new Finder(this, $(this).data("finder")).loadPath(); }); });', 'requirejs', 'string');

        $hash = $this->app['finder']->getToken($root, $mode);

        return "<div data-finder='".json_encode(compact('root', 'mode', 'hash'))."'></div>";
    }
}