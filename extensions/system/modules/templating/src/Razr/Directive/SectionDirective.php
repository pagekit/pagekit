<?php

namespace Pagekit\Templating\Razr\Directive;

use Razr\Directive\Directive;
use Razr\Token;
use Razr\TokenStream;

class SectionDirective extends Directive
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->name = 'section';
    }

    /**
     * @{inheritdoc}
     */
    public function parse(TokenStream $stream, Token $token)
    {
        if ($stream->nextIf('section') && $stream->expect('(')) {

            $stack = [true];

            while (!empty($stack) && $token = $stream->peekUntil(T_COMMENT, '/* DIRECTIVE */')) {

                if ($token = $stream->peek()) {

                    if ($token->test('section')) {
                        $stack[] = true;
                    }

                    if ($token->test('endsection')) {
                        array_pop($stack);
                    }

                }

            }

            $stream->resetPeek();

            return sprintf("\$app['sections']->%s%s", empty($stack) ? 'start' : 'output', $this->parser->parseExpression());
        }

        if ($stream->nextIf('endsection')) {
            return "echo(\$app['sections']->end())";
        }
    }
}
