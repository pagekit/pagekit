<?php

namespace Pagekit\Filesystem\Archive;

use Pagekit\Filesystem\Exception\RuntimeException;

class Zip implements ArchiveInterface
{
    /**
     * {@inheritdoc}
     */
    public static function extract($archive, $path)
    {
        if (!class_exists('ZipArchive')) {
            throw new RuntimeException('You need the zip extension enabled');
        }

        $zip = new \ZipArchive;

        if (true !== ($error = $zip->open($archive))) {
            return $error;
        }

        return $zip->extractTo($path) ? $zip->close() : false;
    }

    /**
     * Give a meaningful error message to the user.
     *
     * @param  int $error
     * @return string
     */
    protected static function getErrorMessage($error)
    {
        switch ($error) {
            case \ZipArchive::ER_EXISTS:
                return sprintf("File already exists");
            case \ZipArchive::ER_INCONS:
                return sprintf("Zip archive is inconsistent");
            case \ZipArchive::ER_INVAL:
                return sprintf("Invalid argument");
            case \ZipArchive::ER_MEMORY:
                return sprintf("Memory allocation failure");
            case \ZipArchive::ER_NOENT:
                return sprintf("No such ZIP file");
            case \ZipArchive::ER_NOZIP:
                return sprintf("Is not a ZIP archive");
            case \ZipArchive::ER_OPEN:
                return sprintf("Can't open ZIP file");
            case \ZipArchive::ER_READ:
                return sprintf("Zip read error");
            case \ZipArchive::ER_SEEK:
                return sprintf("Zip seek error");
            case \ZipArchive::ER_MULTIDISK:
                return sprintf("Multidisk ZIP Archives not supported");
            case \ZipArchive::ER_RENAME:
                return sprintf("Failed to rename the temporary file for ZIP");
            case \ZipArchive::ER_CLOSE:
                return sprintf("Failed to close the ZIP Archive");
            case \ZipArchive::ER_WRITE:
                return sprintf("Failure while writing the ZIP Archive");
            case \ZipArchive::ER_CRC:
                return sprintf("CRC failure within the ZIP Archive");
            case \ZipArchive::ER_ZIPCLOSED:
                return sprintf("ZIP Archive already closed");
            case \ZipArchive::ER_TMPOPEN:
                return sprintf("Failure creating temporary ZIP Archive");
            case \ZipArchive::ER_CHANGED:
                return sprintf("ZIP Entry has been changed");
            case \ZipArchive::ER_ZLIB:
                return sprintf("ZLib Problem");
            case \ZipArchive::ER_COMPNOTSUPP:
                return sprintf("Compression method not supported within ZLib");
            case \ZipArchive::ER_EOF:
                return sprintf("Premature EOF within ZIP Archive");
            case \ZipArchive::ER_INTERNAL:
                return sprintf("Internal error while working on a ZIP Archive");
            case \ZipArchive::ER_REMOVE:
                return sprintf("Can not remove ZIP Archive");
            case \ZipArchive::ER_DELETED:
                return sprintf("ZIP Entry has been deleted");
            default:
                return sprintf("Not a valid ZIP archive, got error code: %s", $error);
        }
    }
}
