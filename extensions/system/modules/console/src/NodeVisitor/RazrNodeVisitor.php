<?php

namespace Pagekit\Console\NodeVisitor;

class RazrNodeVisitor extends PhpNodeVisitor
{
    /**
     * {@inheritdoc}
     */
    public function loadTemplate($name)
    {
        $this->file = $name;
        return $this->engine->loadTemplate($name);
    }
}