<?php

namespace Pagekit\Feed;

interface FeedInterface
{
    /**
     * @return string
     */
    public function getMimeType();

    /**
     * @param string $mime
     */
    public function setMimeType($mime);

    /**
     * Adds an XML namespace.
     *
     * @param  string $prefix
     * @param  string $uri
     * @return self
     */
    public function addNamespace($prefix, $uri);

    /**
     * Gets the encoding.
     *
     * @return string
     */
    public function getEncoding();

    /**
     * Sets the encoding.
     *
     * @param  string $encoding
     * @return self
     */
    public function setEncoding($encoding);

    /**
     * Gets properties to be enclosed in CDATA.
     *
     * @return string[]
     */
    public function getCDATA();

    /**
     * Adds properties to enclose in CDATA.
     *
     * @param   string[] $properties
     * @return  self
     */
    public function addCDATA(array $properties);

    /**
     * @param  array $elements
     * @return ItemInterface
     */
    public function createItem(array $elements = []);

    /**
     * Adds an item.
     *
     * @param  ItemInterface $item
     * @return self
     */
    public function addItem(ItemInterface $item);

    /**
     * Sets the title.
     *
     * @param  string $title
     * @return self
     */
    public function setTitle($title);

    /**
     * Sets the link.
     *
     * @param  string $link
     * @return self
     */
    public function setLink($link);

    /**
     * Sets the image.
     *
     * @param  string $title
     * @param  string $link
     * @param  string $url
     * @return self
     */
    public function setImage($title, $link, $url);

    /**
     * Sets the description.
     *
     * @param  string $description
     * @return self
     */
    public function setDescription($description);

    /**
     * Sets a link with rel="self".
     *
     * @param  string $href
     * @return self
     */
    public function setSelfLink($href);

    /**
     * Sets a custom link.
     *
     * @param  string $href
     * @param  string $rel
     * @param  string $type
     * @param  string $hreflang
     * @param  string $title
     * @param  int    $length
     * @return self
     */
    public function setAtomLink($href, $rel = '', $type = '', $hreflang = '', $title = '', $length = 0);

    /**
     * Generates the feed.
     *
     * @return string
     */
    public function generate();

    /**
     * Outputs the feed.
     */
    public function output();
}
