<?php

namespace Pagekit\Intl\Loader;

use Symfony\Component\Translation\Exception\InvalidResourceException;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

/**
 * @copyright Copyright (c) 2010, Union of RAD http://union-of-rad.org (http://lithify.me/)
 * @copyright Copyright (c) 2012, Clemens Tolboom
 */
class PoFileLoader extends ArrayLoader
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

        $messages = $this->parse($resource);

        // empty file
        if (null === $messages) {
            $messages = [];
        }

        // not an array
        if (!is_array($messages)) {
            throw new InvalidResourceException(sprintf('The file "%s" must contain a valid po file.', $resource));
        }

        return parent::load($messages, $locale, $domain);
    }

    /**
     * Parses portable object (PO) format.
     *
     * From http://www.gnu.org/software/gettext/manual/gettext.html#PO-Files
     * we should be able to parse files having:
     *
     * white-space
     * #  translator-comments
     * #. extracted-comments
     * #: reference...
     * #, flag...
     * #| msgid previous-untranslated-string
     * msgid untranslated-string
     * msgstr translated-string
     *
     * extra or different lines are:
     *
     * #| msgctxt previous-context
     * #| msgid previous-untranslated-string
     * msgctxt context
     *
     * #| msgid previous-untranslated-string-singular
     * #| msgid_plural previous-untranslated-string-plural
     * msgid untranslated-string-singular
     * msgid_plural untranslated-string-plural
     * msgstr[0] translated-string-case-0
     * ...
     * msgstr[N] translated-string-case-n
     *
     * The definition states:
     * - white-space and comments are optional.
     * - msgid "" that an empty singleline defines a header.
     *
     * This parser sacrifices some features of the reference implementation the
     * differences to that implementation are as follows.
     * - No support for comments spanning multiple lines.
     * - Translator and extracted comments are treated as being the same type.
     * - Message IDs are allowed to have other encodings as just US-ASCII.
     *
     * Items with an empty id are ignored.
     *
     * @param resource $resource
     *
     * @return array
     */
    protected function parse($resource)
    {
        $stream = fopen($resource, 'r');

        $defaults = [
            'ids' => [],
            'translated' => null,
        ];

        $messages = [];
        $item = $defaults;

        while ($line = fgets($stream)) {
            $line = trim($line);

            if ($line === '') {
                // Whitespace indicated current item is done
                $this->addMessage($messages, $item);
                $item = $defaults;
            } elseif (substr($line, 0, 7) === 'msgid "') {
                // We start a new msg so save previous
                // TODO: this fails when comments or contexts are added
                $this->addMessage($messages, $item);
                $item = $defaults;
                $item['ids']['singular'] = substr($line, 7, -1);
            } elseif (substr($line, 0, 8) === 'msgstr "') {
                $item['translated'] = substr($line, 8, -1);
            } elseif ($line[0] === '"') {
                $continues = isset($item['translated']) ? 'translated' : 'ids';

                if (is_array($item[$continues])) {
                    end($item[$continues]);
                    $item[$continues][key($item[$continues])] .= substr($line, 1, -1);
                } else {
                    $item[$continues] .= substr($line, 1, -1);
                }
            } elseif (substr($line, 0, 14) === 'msgid_plural "') {
                $item['ids']['plural'] = substr($line, 14, -1);
            } elseif (substr($line, 0, 7) === 'msgstr[') {
                $size = strpos($line, ']');
                $item['translated'][(integer) substr($line, 7, 1)] = substr($line, $size + 3, -1);
            }

        }
        // save last item
        $this->addMessage($messages, $item);
        fclose($stream);

        // filter empty translations
        return array_filter($messages);
    }

    /**
     * Save a translation item to the messages.
     *
     * A .po file could contain by error missing plural indexes. We need to
     * fix these before saving them.
     *
     * @param array $messages
     * @param array $item
     */
    protected function addMessage(array &$messages, array $item)
    {
        if (is_array($item['translated'])) {
            $messages[$item['ids']['singular']] = stripslashes($item['translated'][0]);
            if (isset($item['ids']['plural'])) {
                $plurals = $item['translated'];
                // PO are by definition indexed so sort by index.
                ksort($plurals);
                // Make sure every index is filled.
                end($plurals);
                $count = key($plurals);
                // Fill missing spots with '-'.
                $empties = array_fill(0, $count+1, '-');
                $plurals += $empties;
                ksort($plurals);
                $messages[$item['ids']['plural']] = stripcslashes(implode('|', $plurals));
            }
        } elseif (!empty($item['ids']['singular'])) {
              $messages[$item['ids']['singular']] = stripslashes($item['translated']);
        }
    }
}
