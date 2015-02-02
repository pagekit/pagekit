<?php

namespace Pagekit\Console\NodeVisitor;

use PhpParser\Lexer;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor as BaseVisitor;
use PhpParser\Parser;

class PhpNodeVisitor extends NodeVisitor implements BaseVisitor
{
    /**
     * {@inheritdoc}
     */
    public function traverse(array $files)
    {
        $parser    = new Parser(new Lexer);
        $traverser = new NodeTraverser;
        $traverser->addVisitor($this);

        foreach ($files as $file) {

            try {

                $traverser->traverse($parser->parse(file_get_contents($this->loadTemplate($file))));

            } catch (\Exception $e) {
            }
        }

        return $this->results;
    }

    /**
     * {@inheritdoc}
     */
    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Expr\FuncCall
            && ($node->name->parts[0] == '__' || $node->name->parts[0] == '_c')
            && isset($node->args[0]) && is_string($string = $node->args[0]->value->value)
        ) {
            $key                               = $node->name->parts[0] == '__' ? 2 : 3;
            $domain                            = isset($node->args[$key]) && is_string($node->args[$key]->value->value) ? $node->args[$key]->value->value : 'messages';
            $this->results[$domain][$string][] = ['file' => $this->file, 'line' => $node->getLine()];
        }
    }

    public function beforeTraverse(array $nodes)
    {
    }

    public function leaveNode(Node $node)
    {
    }

    public function afterTraverse(array $nodes)
    {
    }
}