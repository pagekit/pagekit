<?php

namespace Pagekit\Filesystem\Archive;

interface ArchiveInterface
{
    /**
     * Extracts an archive to a destination path.
     *
     * @param string $archive
     * @param string $path
     *
     * @throws \UnexpectedValueException
     * @throws \RuntimeException
     */
    public static function extract($archive, $path);
}
