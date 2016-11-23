<?php

namespace Pagekit\Feed\Item;

use Pagekit\Feed\Feed;
use Pagekit\Feed\Item;

class RSS1 extends Item
{
    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        return $this;
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
    public function setDate(\DateTimeInterface $date)
    {
        return $this->setElement('dc:date', date('Y-m-d', $date->getTimestamp()));
    }

    /**
     * {@inheritdoc}
     */
    public function setAuthor($author, $email = null, $uri = null)
    {
        return $this;
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
    public function addEnclosure($url, $length, $type, $multiple = true)
    {
        return $this;
    }
}
