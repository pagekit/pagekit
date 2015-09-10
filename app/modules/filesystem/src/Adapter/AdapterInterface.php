<?php

namespace Pagekit\Filesystem\Adapter;

interface AdapterInterface
{
    /**
     * Gets stream wrapper classname.
     *
     * @return string
     */
    public function getStreamWrapper();

    /**
     * Gets file path info.
     *
     * @param  array $info
     * @return array
     */
    public function getPathInfo(array $info);
}
