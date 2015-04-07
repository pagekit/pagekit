<?php

namespace Pagekit\Site\Model;

use Pagekit\Application as App;

class MountType extends Type
{
    protected $controllers;

    public function __construct($id, $label, $controllers, array $options = [])
    {
        parent::__construct($id, $label, $options);
        $this->controllers = (array) $controllers;
    }

    public function getControllers()
    {
        return $this->controllers;
    }

    public function bind(NodeInterface $node)
    {
        App::controllers()->mount($node->getPath(), $this->getControllers(), "@{$this->getId()}/", $node->get('defaults'));
    }

    public function jsonSerialize()
    {
        return array_merge(parent::jsonSerialize(), ['type' => 'mount']);
    }
}
