<?php

namespace Pagekit\Site\Model;

use Pagekit\Application as App;

class MountType extends Type
{
    protected $controllers;

    public function __construct($id, $label, $controllers, $url = '', array $options = [])
    {
        parent::__construct($id, $label, $url, $options);
        $this->controllers = (array) $controllers;
    }

    public function getControllers()
    {
        return $this->controllers;
    }

    public function bind(NodeInterface $node)
    {
        App::controllers()->mount($node->getPath(), $this->getControllers(), "@{$this->getId()}", $node->get('defaults', []));
    }
}
