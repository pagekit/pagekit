<?php

namespace Pagekit\Site\Model;

interface NodeInterface
{
    public function getId();

    public function getParentId();

    public function getPriority();

    public function getStatus();

    public function getTitle();

    public function getSlug();

    public function getPath();

    public function getType();

    public function getMenu();
}
