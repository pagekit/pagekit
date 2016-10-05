<?php

namespace Pagekit\Feed\Feed;

use Pagekit\Feed\Feed;

class RSS1 extends Feed
{
    protected $mime = 'application/rdf+xml';
    protected $item = 'Pagekit\Feed\Item\RSS1';

    /**
     * @var string
     */
    protected $about;

    /**
     * @return string
     */
    public function getAbout()
    {
        return $this->about;
    }

    /**
     * @param string $about
     */
    public function setAbout($about)
    {
        $this->about = $about;
    }

    /**
     * {@inheritdoc}
     */
    public function setDate(\DateTimeInterface $date)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function build()
    {
        $doc = new \DOMDocument('1.0', $this->encoding);

        $root = $doc->appendChild($doc->createElementNS($this->namespaces['rdf'], 'rdf:RDF'));

        $root->setAttribute('xmlns', $this->namespaces['rss1']);

        if (!$about = $this->about) {
            if (!isset($this->elements['link'])) {
                throw new \RuntimeException('RSS1 requires a link element.');
            }

            $about = $this->elements['link'][0][1];
        }

        $channel = $root->appendChild($doc->createElement('channel'));
        $channel->setAttribute('rdf:about', $about);

        foreach ($this->getElements() as $element) {

            if (is_array($element[1]) && isset($element[1]['link'])) {

                $channel->appendChild($doc->createElement($element[0]))->setAttribute('rdf:resource', $element[1]['link']);
                $root->appendChild($this->buildElement($doc, $element))->setAttribute('rdf:about', $element[1]['link']);

            } else {
                $channel->appendChild($this->buildElement($doc, $element));
            }
        }

        $items = $channel->appendChild($doc->createElement('items'))->appendChild($doc->createElement('rdf:Seq'));

        foreach ($this->items as $feedItem) {

            $item = $root->appendChild($doc->createElement('item'));

            foreach ($feedItem->getElements() as $element) {

                if ($element[0] == 'link') {
                    $items->appendChild($doc->createElement('rdf:li'))->setAttribute('resource', $element[1]);

                    $item->setAttribute('rdf:about', $element[1]);
                }

                $item->appendChild($this->buildElement($doc, $element));
            }

            if (!$item->getAttribute('rdf:about')) {
                throw new \RuntimeException('RSS1 items require a link element.');
            }
        }

        return $doc;
    }
}
