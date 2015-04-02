<?php

namespace Pagekit\Site\Model;

interface NodeInterface
{
    /**
     * Returns the id.
     *
     * @return mixed
     */
    public function getId();

    /**
     * Returns the title.
     *
     * @return string
     */
    public function getTitle();
}
