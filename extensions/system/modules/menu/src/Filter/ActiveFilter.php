<?php

namespace Pagekit\Menu\Filter;

use Pagekit\Application as App;

class ActiveFilter extends FilterIterator
{
    /**
     * @var array
     */
    protected $active;

    /**
     * {@inheritdoc}
     */
    public function __construct(\Iterator $iterator, array $options = [])
    {
        parent::__construct($iterator, $options);

        if (isset($options['active']) && $options['active']) {
            $this->active = App::request()->attributes->get('_menu', []);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function accept()
    {
        if (null === $this->active) {
            return true;
        }

        $item = parent::current();

        if (in_array($item->getId(), $this->active)) {
            $item->setAttribute('active', true);
        }

        return true;
    }
}
