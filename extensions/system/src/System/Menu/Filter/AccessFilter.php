<?php

namespace Pagekit\System\Menu\Filter;

use Pagekit\Application as App;
use Pagekit\Menu\Filter\FilterIterator;

class AccessFilter extends FilterIterator
{
    /**
     * {@inheritdoc}
     */
    public function accept()
    {
        if (!isset($this->options['access']) or !$this->options['access']) {
            return true;
        }

        return App::user()->hasAccess(parent::current()->getAccess());
    }
}
