<?php

namespace Pagekit\Menu\Filter;

use Pagekit\Framework\Application as App;

class AccessFilter extends FilterIterator
{
    /**
     * {@inheritdoc}
     */
    public function accept()
    {
        return !isset($this->options['access']) || !$this->options['access'] || parent::current()->hasAccess(App::user());
    }
}
