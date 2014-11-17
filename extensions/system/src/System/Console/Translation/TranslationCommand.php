<?php

namespace Pagekit\System\Console\Translation;

use Pagekit\Framework\Console\Command;
use Symfony\Component\Finder\Finder;


/**
 * An abstract class including a few helper methods to be used
 * by commands taking care of translating extensions.
 */
abstract class TranslationCommand extends Command
{

    /**
     * Returns the extension path.
     *
     * @param  string $path
     * @return array
     */
    protected function getPath($path)
    {
        $root = $this->pagekit['path.extensions'];

        if (!is_dir($path = "$root/$path")) {
            $this->error("Can't find extension in '$path'");
            exit;
        }

        return $path;
    }

    /**
     * Returns all files of an extension to extract translations.
     *
     * @param  string $path
     * @return array
     */
    protected function getFiles($path)
    {
        $files = [];

        foreach (Finder::create()->files()->in($path)->name('*.{razr,php}') as $file) {

            $file = $file->getPathname();

            foreach ($this->visitors as $name => $visitor) {
                if ($visitor->getEngine()->supports($file)) {
                    $files[$name][] = $file;
                    break;
                }
            }
        }

        return $files;
    }

    /**
     * Returns the relative path to the root.
     *
     * @param  string $path
     * @return string
     */
    protected function getRelativePath($path)
    {
        $root = $this->pagekit['path'];

        if (0 === strpos($path, $root)) {
            $path = ltrim(str_replace('\\', '/', substr($path, strlen($root))), '/');
        }

        return $path;
    }
}