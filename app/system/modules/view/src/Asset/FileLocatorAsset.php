<?php

namespace Pagekit\View\Asset;

use Pagekit\Application as App;

class FileLocatorAsset extends FileAsset
{
    /**
     * {@inheritdoc}
     */
    public function getSource()
    {
        return ($path = $this->getPath()) ? App::file()->getUrl($path) : parent::getSource();
    }

    /**
     * {@inheritdoc}
     */
    protected function getPath()
    {
        if (!isset($this->options['path']) && $path = App::locator()->get($this->source)) {
            return $path;
        }

        return parent::getPath();
    }
}
