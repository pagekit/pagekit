<?php

namespace Pagekit\System\Menu\Filter;

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

        return $this['user']->hasAccess(parent::current()->getAccess());
    }
}
