<?php

namespace Pagekit\Feed;

abstract class Item implements ItemInterface
{
    use ElementsTrait;

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
    public function setContent($content)
    {
        return $this;
    }
}
