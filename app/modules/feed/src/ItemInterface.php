<?php

namespace Pagekit\Feed;

interface ItemInterface
{
    /**
     * Sets the id.
     *
     * @param  string $id
     * @return self
     */
    public function setId($id);

    /**
     * Sets the title.
     *
     * @param  string $title
     * @return self
     */
    public function setTitle($title);

    /**
     * Sets the description.
     *
     * @param  string $description
     * @return self
     */
    public function setDescription($description);

    /**
     * Sets the content.
     *
     * @param  string $content
     * @return self
     */
    public function setContent($content);

    /**
     * Sets the date.
     *
     * @param  \DateTimeInterface $date
     * @return self
     */
    public function setDate(\DateTimeInterface $date);

    /**
     * Sets the author.
     *
     * @param  string $author
     * @param  string $email
     * @param  string $uri
     * @return self
     */
    public function setAuthor($author, $email = null, $uri = null);

    /**
     * Sets the link.
     *
     * @param  string $link
     * @return self
     */
    public function setLink($link);

    /**
     * Adds an attachment.
     *
     * @param  string  $url
     * @param  integer $length
     * @param  string  $type
     * @param  bool    $multiple
     * @return self
     */
    public function addEnclosure($url, $length, $type, $multiple = true);

    /**
     * Sets an element.
     *
     * @param  string $name
     * @param  string $value
     * @param  null   $attributes
     * @return self
     */
    public function setElement($name, $value, $attributes = null);

    /**
     * Adds an element.
     *
     * @param  string $name
     * @param  string $value
     * @param  null   $attributes
     * @return self
     */
    public function addElement($name, $value, $attributes = null);

    /**
     * Adds multiple elements.
     *
     * @param  $elements array
     * @return self
     */
    public function addElements(array $elements);

    /**
     * Gets the items elements.
     *
     * @return array[]
     */
    public function getElements();
}
