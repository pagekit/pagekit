<?php

namespace Pagekit\View\Helper;

use Pagekit\Markdown\Markdown;

class MarkdownHelper extends Helper
{
    /**
     * @var Markdown
     */
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
    public function __invoke($text)
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
