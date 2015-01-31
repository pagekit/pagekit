<?php

namespace Pagekit\Templating\Razr\Directive;

use Razr\Directive\Directive;
use Razr\Token;
use Razr\TokenStream;

class TransDirective extends Directive
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->name = 'trans';
    }

    /**
     * @{inheritdoc}
     */
    public function parse(TokenStream $stream, Token $token)
    {
        if ($stream->nextIf(['trans', 'transchoice']) && $stream->expect('(')) {

            $out = 'echo ' . ($token->test('trans') ? '__' : '_c');

            while (!$stream->test(T_CLOSE_TAG)) {
                $out .= $this->parser->parseExpression();
            }

            return $out;
        }
    }
}
