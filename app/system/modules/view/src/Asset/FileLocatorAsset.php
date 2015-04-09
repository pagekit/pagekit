<?php

namespace Pagekit\View\Asset;

use Pagekit\Application as App;

class FileLocatorAsset extends FileAsset
{
    /**
     * {@inheritdoc}
     */
    public function __construct($name, $source, array $dependencies = [], array $options = [])
    {
        if (!isset($options['path']) && $path = App::locator()->get($source)) {
            $options['path'] = $path;
        }

        parent::__construct($name, App::url()->getStatic($source), $dependencies, $options);
    }
}
