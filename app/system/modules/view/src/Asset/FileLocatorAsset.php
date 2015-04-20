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
    public function getPath()
    {
        return App::locator()->get($this->source) ?: false;
    }
}
