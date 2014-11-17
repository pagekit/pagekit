<?php

namespace Pagekit\System\Console\Translation;

class RazrNodeVisitor extends PhpNodeVisitor implements \PhpParser\NodeVisitor
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