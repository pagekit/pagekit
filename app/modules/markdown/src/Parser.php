<?php

namespace Pagekit\Markdown;

use Pagekit\Markdown\Lexer\InlineLexer;

class Parser
{
    protected $token;
    protected $tokens;
    protected $inline;
    protected $renderer;
    protected $options;

    /**
     * Constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->options = $options;
        $this->renderer = $options['renderer'];
    }

    /**
     * Compiling method.
     *
     * @param  array $src
     * @return string
     */
    public function parse($src)
    {
        $this->inline = new InlineLexer($src['links'], $this->options);

        unset($src['links']);

        $this->tokens = array_reverse($src);

        $out = '';

        while ($this->next()) {
            $out .= $this->tok();
        }

        return $out;
    }

    /**
     * Next token.
     *
     * @return array
     */
    protected function next()
    {
        return $this->token = array_pop($this->tokens);
    }

    /**
     * Preview next token.
     *
     * @return array
     */
    protected function peek()
    {
        return end($this->tokens);
    }

    /**
     * Parse text tokens.
     *
     * @return string
     */
    protected function parseText()
    {
        $body = $this->token['text'];

        while (($token = $this->peek()) && $token['type'] == 'text') {
          $body .= "\n".$token['text'];
          $this->next();
        }

        return $this->inline->output($body);
    }

    /**
     * Parse current token.
     *
     * @return string
     */
    protected function tok()
    {
        $body = '';

        switch ($this->token['type']) {

            case 'space':

                return '';

            case 'hr':

                return $this->renderer->hr();

            case 'heading':

                return $this->renderer->heading($this->inline->output($this->token['text']), $this->token['depth'], $this->token['text']);

            case 'code':

                return $this->renderer->code($this->token['text'], @$this->token['lang'], @$this->token['escaped']);

            case 'table':

                $header = '';
                $cell   = '';

                for ($i = 0; $i < count($this->token['header']); $i++) {
                    $flags = ['header' => true, 'align' => $this->token['align'][$i]];
                    $cell .= $this->renderer->tablecell($this->inline->output($this->token['header'][$i]), $flags);
                }

                $header .= $this->renderer->tablerow($cell);

                for ($i = 0; $i < count($this->token['cells']); $i++) {

                    $row  = $this->token['cells'][$i];
                    $cell = '';

                    for ($j = 0; $j < count($row); $j++) {
                        $flags = ['header' => false, 'align' => $this->token['align'][$j]];
                        $cell .= $this->renderer->tablecell($this->inline->output($row[$j]), $flags);
                    }

                    $body .= $this->renderer->tablerow($cell);
                }

                return $this->renderer->table($header, $body);

            case 'blockquote_start':

                while ($this->next() && $this->token['type'] !== 'blockquote_end') {
                    $body .= $this->tok();
                }

                return $this->renderer->blockquote($body);

            case 'list_start':

                $ordered = $this->token['ordered'];

                while ($this->next() && $this->token['type'] !== 'list_end') {
                    $body .= $this->tok();
                }

                return $this->renderer->lst($body, $ordered);

            case 'list_item_start':

                while ($this->next() && $this->token['type'] !== 'list_item_end') {
                    $body .= ($this->token['type'] === 'text') ? $this->parseText() : $this->tok();
                }

                return $this->renderer->listitem($body);

            case 'loose_item_start':

                while ($this->next() && $this->token['type'] !== 'list_item_end') {
                    $body .= $this->tok();
                }

                return $this->renderer->listitem($body);

            case 'html':

                $html = (!$this->token['pre'] && !$this->options['pedantic']) ? $this->inline->output($this->token['text']) : $this->token['text'];

                return $this->renderer->html($html);

            case 'paragraph':

                return $this->renderer->paragraph($this->inline->output($this->token['text']));

            case 'text':

                return $this->renderer->paragraph($this->parseText());
        }
    }
}
