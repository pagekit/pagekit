<?php

namespace Pagekit\Site\Model;

interface NodeInterface
{
    public function getId();
    public function getTitle();
    public function getPath();
}
