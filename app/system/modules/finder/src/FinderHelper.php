<?php

namespace Pagekit\Finder;

use Pagekit\Application as App;
use Pagekit\View\Helper\Helper;

class FinderHelper extends Helper
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

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'finder';
    }
}
