<?php

namespace Pagekit\System\Migration;

use Pagekit\Component\File\ResourceLocator;
use Pagekit\Component\Migration\Loader\FilesystemLoader as BaseLoader;

class FilesystemLoader extends BaseLoader
{
    /**
     * @var ResourceLocator
     */
    protected $locator;

    /**
     * Constructor.
     *
     * @param ResourceLocator $locator
     */
    public function __construct(ResourceLocator $locator)
    {
        $this->locator = $locator;
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfig($filename, $parameters)
    {
        return parent::getConfig($this->locator->findResource($filename), $parameters);
    }
}
