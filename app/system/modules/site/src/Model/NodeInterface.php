<?php

namespace Pagekit\Site\Model;

use Pagekit\System\Model\NodeInterface as BaseNodeInterface;

interface NodeInterface extends BaseNodeInterface
{
    public function getId();

    public function getParentId();

    public function getPriority();

    public function getStatus();

    public function getTitle();

    public function getSlug();

    public function getPath();

    public function getLink();

    public function getType();

    public function getMenu();
}
