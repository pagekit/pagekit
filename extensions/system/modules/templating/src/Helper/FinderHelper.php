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
     * @param  string $mode
     * @return string
     */
    public function render($root, $mode = 'r')
    {
        App::scripts('finder');

        return "<div data-finder='".json_encode(compact('root', 'mode'))."'></div>";
    }
}
