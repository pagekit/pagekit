<?php

namespace Pagekit\Site\Model;

use Pagekit\Application as App;

class UrlType extends Type
{
    public function bind(NodeInterface $node)
    {
        App::aliases()->add($node->getPath(), $this->getLink($node), $node->get('defaults', []));
    }
}
