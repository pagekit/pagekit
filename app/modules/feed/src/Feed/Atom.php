<?php

namespace Pagekit\Feed\Feed;

use Pagekit\Feed\Feed;

class Atom extends Feed
{
    protected $mime = 'application/atom+xml';
    protected $item = 'Pagekit\Feed\Item\Atom';

    /**
     * {@inheritdoc}
     */
    public function setDate(\DateTimeInterface $date)
    {
        return $this->setElement('updated', $date->format(\DATE_ATOM));
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($description)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setAtomLink($href, $rel = '', $type = '', $hreflang = '', $title = '', $length = 0)
    {
        return parent::setAtomLink($href, $rel, $type, $hreflang, $title, $length)->setElement('id', self::uuid($href, 'urn:uuid:'));
    }

    /**
     * Generates an UUID.
     *
     * @param  string $key
     * @param  string $prefix
     * @return string
     */
    public static function uuid($key = null, $prefix = '')
    {
        $hash = str_split(md5($key ?: uniqid()), 4);
        foreach ([2, 1, 1, 1, 3] as $length) {
            $uuid[] = implode('', array_splice($hash, 0, $length));
        }
        return $prefix.implode('-', $uuid);
    }

    /**
     * {@inheritdoc}
     */
    protected function build()
    {
        $doc = new \DOMDocument('1.0', $this->encoding);

        $root = $doc->appendChild($doc->createElement('feed'));
        $root->setAttribute('xmlns', $this->namespaces['atom']);

        foreach ($this->getElements() as $element) {
            $root->appendChild($this->buildElement($doc, $element));
        }

        foreach ($this->items as $item) {
            $entry = $root->appendChild($doc->createElement('entry'));
            foreach ($item->getElements() as $element) {
                $entry->appendChild($this->buildElement($doc, $element));
            }
        }

        return $doc;
    }

    /**
     * @param  \DOMDocument $doc
     * @param  array        $element
     * @return \DOMElement
     */
    protected function buildElement(\DOMDocument $doc, array $element)
    {
        $element[0] = 0 === strpos($element[0], 'atom:') ? substr($element[0], 5) : $element[0];
        return parent::buildElement($doc, $element);
    }

    /**
     * {@inheritdoc}
     */
    protected function buildAttributes(\DOMElement $element, array $attributes = [])
    {
        if (in_array($element->nodeName, $this->cdata)) {
            $attributes['type'] = 'html';
        }

        return parent::buildAttributes($element, $attributes);
    }
}
