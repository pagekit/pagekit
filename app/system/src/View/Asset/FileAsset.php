<?php

namespace Pagekit\System\View\Asset;

use Pagekit\Application as App;
use Pagekit\View\Asset\FileAsset as BaseFileAsset;

class FileAsset extends BaseFileAsset
{
    /**
     * {@inheritdoc}
     */
    public function __construct($name, $source, array $dependencies = [], array $options = [])
    {
        if (!isset($options['path']) && $path = App::locator()->get($source)) {
            $options['path'] = $path;
        }

        parent::__construct($name, $source, $dependencies, $options);
    }
}
