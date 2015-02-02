<?php

namespace Pagekit\Locale\Loader;

use Symfony\Component\Translation\Exception\InvalidResourceException;
use Symfony\Component\Translation\Exception\NotFoundResourceException;
use Symfony\Component\Translation\Loader\ArrayLoader;
use Symfony\Component\Translation\Loader\LoaderInterface;

/**
 * PhpFileLoader loads translations from PHP files returning an array of translations.
 *
 * @copyright Copyright (c) 2004-2014 Fabien Potencier
 */
class PhpFileLoader extends ArrayLoader implements LoaderInterface
{
    /**
     * {@inheritdoc}
     */
    public function load($resource, $locale, $domain = 'messages')
    {
        if (!stream_is_local($resource)) {
            throw new InvalidResourceException(sprintf('This is not a local file "%s".', $resource));
        }

        if (!file_exists($resource)) {
            throw new NotFoundResourceException(sprintf('File "%s" not found.', $resource));
        }

        $messages = array_filter(require $resource);

        return parent::load($messages, $locale, $domain);
    }
}
