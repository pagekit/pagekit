<?php

namespace Pagekit\Site\Model;

interface TypeInterface extends \JsonSerializable
{
    public function getId();

    public function getLabel();

    public function getOptions();

    public function getDefaults(NodeInterface $node);

    public function getLink(NodeInterface $node);

    public function bind(NodeInterface $node);
}
