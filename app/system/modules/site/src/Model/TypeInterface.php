<?php

namespace Pagekit\Site\Model;

interface TypeInterface extends \JsonSerializable
{
    public function getId();
    public function getLabel();
    public function getOptions();
    public function bind(NodeInterface $node);
}
