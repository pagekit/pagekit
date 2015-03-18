<?php

namespace Pagekit\System;

use Pagekit\Application as App;
use Pagekit\Module\Module;
use Pagekit\View\Section\SectionManager;

class Theme extends Module
{
    /**
     * @var string
     */
    protected $layout = '/templates/template.razr';

    /**
     * Returns the theme layout absolute path.
     *
     * @return string|false
     */
    public function getLayout()
    {
        return $this->path.$this->layout;
    }
}
