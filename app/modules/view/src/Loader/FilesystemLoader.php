<?php

namespace Pagekit\View\Loader;

use Pagekit\Filesystem\Locator;
use Symfony\Component\Templating\Loader\LoaderInterface;
use Symfony\Component\Templating\Storage\FileStorage;
use Symfony\Component\Templating\TemplateReferenceInterface;

class FilesystemLoader implements LoaderInterface
{
    protected $locator;

    /**
     * Constructor.
     *
     * @param Locator $locator
     */
    public function __construct(Locator $locator)
    {
        $this->locator = $locator;
    }

    /**
     * {@inheritdoc}
     */
    public function load(TemplateReferenceInterface $template)
    {
        if (!strpos($template, ':') && $file = $this->locator->get("views:{$template}")) {
            return new FileStorage($file);
        } elseif ($file = $this->locator->get($template)) {
            return new FileStorage($file);
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isFresh(TemplateReferenceInterface $template, $time)
    {
        if (false === $storage = $this->load($template)) {
            return false;
        }

        if (!is_readable((string) $storage)) {
            return false;
        }

        return filemtime((string) $storage) < $time;
    }
}
