<?php

namespace Pagekit\Templating\Helper;

use Pagekit\Markdown\Markdown;
use Symfony\Component\Templating\Helper\Helper;

class MarkdownHelper extends Helper
{
    protected $parser;

    /**
     * Constructor.
     *
     * @param Markdown $parser
     */
    public function __construct(Markdown $parser)
    {
        $this->parser = $parser;
    }

    /**
     * Parses the markdown syntax and returns HTML.
     *
     * @param  string $text
     * @return string
     */
    public function parse($text)
    {
        return $this->parser->parse($text);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'markdown';
    }
}
