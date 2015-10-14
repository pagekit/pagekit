<?php

namespace Pagekit\Feed;

abstract class Feed implements FeedInterface
{
    use ElementsTrait;

    const ATOM = 'atom';
    const RSS1 = 'rss1';
    const RSS2 = 'rss2';

    /**
     * @var string
     */
    protected $encoding = 'utf-8';

    /**
     * @var string
     */
    protected $mime = '';

    /**
     * @var ItemInterface[]
     */
    protected $items = [];

    /**
     * @var string[]
     */
    protected $cdata = ['description', 'content:encoded', 'summary'];

    /**
     * @var string[]
     */
    protected $namespaces = [
        'content' => 'http://purl.org/rss/1.0/modules/content/',
        'wfw'     => 'http://wellformedweb.org/CommentAPI/',
        'atom'    => 'http://www.w3.org/2005/Atom',
        'rdf'     => 'http://www.w3.org/1999/02/22-rdf-syntax-ns#',
        'rss1'    => 'http://purl.org/rss/1.0/',
        'dc'      => 'http://purl.org/dc/elements/1.1/',
        'sy'      => 'http://purl.org/rss/1.0/modules/syndication/'
    ];

    /**
     * @var string
     */
    protected $item;

    /**
     * {@inheritdoc}
     */
    public function getMimeType()
    {
        return $this->mime;
    }

    /**
     * {@inheritdoc}
     */
    public function setMimeType($mime)
    {
        $this->mime = $mime;
    }

    /**
     * {@inheritdoc}
     */
    public function addNamespace($prefix, $uri)
    {
        $this->namespaces[$prefix] = $uri;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * {@inheritdoc}
     */
    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCDATA()
    {
        return $this->cdata;
    }

    /**
     * {@inheritdoc}
     */
    public function addCDATA(array $properties)
    {
        $this->cdata += $properties;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function createItem(array $elements = [])
    {
        return (new $this->item)->addElements($elements);
    }

    /**
     * {@inheritdoc}
     */
    public function addItem(ItemInterface $item)
    {
        $this->items[] = $item;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setTitle($title)
    {
        return $this->setElement('title', $title);
    }

    /**
     * {@inheritdoc}
     */
    public function setLink($link)
    {
        return $this->setElement('link', $link);
    }

    /**
     * {@inheritdoc}
     */
    public function setImage($title, $link, $url)
    {
        return $this->setElement('image', compact('title', 'link', 'url'));
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($description)
    {
        return $this->setElement('description', $description);
    }

    /**
     * {@inheritdoc}
     */
    public function setSelfLink($href)
    {
        return $this->setAtomLink($href, 'self', $this->getMimeType());
    }

    /**
     * {@inheritdoc}
     */
    public function setAtomLink($href, $rel = '', $type = '', $hreflang = '', $title = '', $length = 0)
    {
        return $this->setElement('atom:link', null, array_filter(compact('href', 'rel', 'type', 'hreflang', 'title', 'length')));
    }

    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        $doc = $this->build();

        $doc->preserveWhiteSpace = false;
        $doc->formatOutput       = true;

        return $doc->saveXML();
    }

    /**
     * {@inheritdoc}
     */
    public function output()
    {
        header("Content-Type: ".$this->getMimeType()."; charset=".$this->encoding);
        echo $this->generate();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->generate();
    }

    /**
     * @return \DOMDocument
     */
    abstract protected function build();

    /**
     * @param  \DOMDocument $doc
     * @param  array        $element
     * @return \DOMElement
     */
    protected function buildElement(\DOMDocument $doc, array $element)
    {
        list($name, $value, $attributes) = $element;

        if ($namespace = strtok($name, ':') and isset($this->namespaces[$namespace])) {
            $doc->createAttributeNS($this->namespaces[$namespace], $namespace.':attr');
        }

        if (is_array($value)) {

            $item = $doc->createElement($name);

            foreach ($value as $tag => $child) {
                $item->appendChild($this->buildElement($doc, [$tag, $child, []]));
            }

        } elseif (in_array($name, $this->cdata)) {
            $item = $doc->createElement($name);
            $item->appendChild($doc->createCDATASection($value));
        } else {
            $item = $doc->createElement($name);
            $item->appendChild($doc->createTextNode($value));
        }

        return $this->buildAttributes($item, (array) $attributes);
    }

    /**
     * @param  \DOMElement $element
     * @param  array       $attributes
     * @return \DOMElement
     */
    protected function buildAttributes(\DOMElement $element, array $attributes = [])
    {
        foreach ($attributes as $name => $value) {
            $element->setAttribute($name, $value);
        }
        return $element;
    }
}
