<?php

namespace Pagekit\Site\Model;

use Pagekit\Application as App;

class UrlType extends Type
{
    protected $url;

    public function __construct($id, $label, $url, array $options = [])
    {
        parent::__construct($id, $label, $options);

        $this->url = $url;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function bind(NodeInterface $node)
    {
        App::aliases()->add($node->getPath(), $this->getUrl(), $node->get('defaults'));
    }
}
