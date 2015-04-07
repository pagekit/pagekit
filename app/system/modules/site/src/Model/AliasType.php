<?php

namespace Pagekit\Site\Model;

use Pagekit\Application as App;

class AliasType extends Type
{
    public function bind(NodeInterface $node)
    {
        App::aliases()->add($node->getPath(), $node->get('url'), $node->get('defaults'));
    }
}
