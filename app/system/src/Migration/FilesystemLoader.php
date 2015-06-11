<?php

namespace Pagekit\System\Migration;

use Pagekit\Application as App;
use Pagekit\Migration\Loader\FilesystemLoader as BaseLoader;

class FilesystemLoader extends BaseLoader
{
    /**
     * {@inheritdoc}
     */
    public function load($path, $pattern, $parameters = array())
    {
        return parent::load(App::locator()->get($path), $pattern, $parameters);
    }
}
