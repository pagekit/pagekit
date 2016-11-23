<?php

namespace Pagekit\Feed\Item;

use Pagekit\Feed\Feed;
use Pagekit\Feed\Item;

class RSS2 extends Item
{
    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        return $this->addElement('guid', $id, ['isPermaLink' => 'true']);
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
        return $this->setElement('pubDate', date(\DATE_RSS, $date->getTimestamp()));
    }

    /**
     * {@inheritdoc}
     */
    public function setAuthor($author, $email = null, $uri = null)
    {
        return $this->setElement('author', $email ? $email.' ('.$author.')' : $author);
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
        return $this->addElement('enclosure', '', compact('url', 'length', 'type'), false, $multiple);
    }
}
