<?php

namespace Pagekit\Finder;

use Pagekit\Application as App;
use Pagekit\View\Helper\HelperInterface;

class FinderHelper implements HelperInterface
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
