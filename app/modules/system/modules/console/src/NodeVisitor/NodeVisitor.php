<?php

namespace Pagekit\Console\NodeVisitor;

use PhpParser\Lexer;
use PhpParser\Node;
use Symfony\Component\Templating\EngineInterface;

abstract class NodeVisitor
{
    /**
     * @var string
     */
    public $file;

    /**
     * @var array
     */
    public $results = [];

    /**
     * @var EngineInterface
     */
    public $engine;

    public function __construct(EngineInterface $engine)
    {
        $this->engine = $engine;
    }

    /**
     * @return EngineInterface
     */
    public function getEngine()
    {
        return $this->engine;
    }

    /**
     * Starts traversing an array of files.
     *
     * @param  array $files
     * @return array
     */
    abstract public function traverse(array $files);

    /**
     * @param  string $name
     * @return string
     */
    protected function loadTemplate($name)
    {
        return $this->file = $name;
    }
}