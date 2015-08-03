<?php

namespace Pagekit\System;

use Pagekit\Application as App;
use Pagekit\Module\Module;

class Theme extends Module
{
    /**
     * @var string
     */
    protected $layout = '/views/template.php';

    /**
     * Gets the layout path.
     *
     * @return string
     */
    public function getLayout()
    {
        return $this->path.$this->layout;
    }
}
