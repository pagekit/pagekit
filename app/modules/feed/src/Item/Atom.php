<?php

namespace Pagekit\Feed\Item;

use Pagekit\Feed\Feed;
use Pagekit\Feed\Item;

class Atom extends Item
{
    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        return $this->setElement('id', Feed\Atom::uuid($id, 'urn:uuid:'));
    }

    /**
     * {@inheritdoc}
     */
    public function setElement($name, $value, $attributes = null)
    {
        return parent::setElement($this->removeNamespace($name), $value, $attributes);
    }

    /**
     * @param  string $name
     * @return string
     */
    protected function removeNamespace($name)
    {
        return 0 === strpos($name, 'atom:') ? substr($name, 5) : $name;
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($description)
    {
        return $this->setElement('summary', $description);
    }

    /**
     * {@inheritdoc}
     */
    public function setContent($content)
    {
        return $this->setElement('content', $content, ['type' => 'html']);
    }

    /**
     * {@inheritdoc}
     */
    public function setDate(\DateTimeInterface $date)
    {
        return $this->setElement('updated', date(\DATE_ATOM, $date->getTimestamp()));
    }

    /**
     * {@inheritdoc}
     */
    public function setAuthor($name, $email = null, $uri = null)
    {
        return $this->setElement('author', array_filter(compact('name', 'email', 'uri')));
    }

    /**
     * {@inheritdoc}
     */
    public function setLink($link)
    {
        return $this
            ->setElement('link', '', ['href' => $link])
            ->setId($link);
    }

    /**
     * {@inheritdoc}
     */
    public function addEnclosure($url, $length, $type, $multiple = true)
    {
        return $this->addElement('atom:link', '', [
            'length' => $length,
            'type'   => $type,
            'href'   => $url,
            'rel'    => 'enclosure'
        ], false, $multiple);
    }

    /**
     * {@inheritdoc}
     */
    public function addElement($name, $value, $attributes = null)
    {
        return parent::addElement($this->removeNamespace($name), $value, $attributes);
    }
}
