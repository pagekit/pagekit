<?php

namespace Pagekit\Markdown\Lexer;

class BlockLexer
{
    protected $rules;
    protected $tokens;
    protected $options;

    protected static $blocks;

    /**
     * Constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->options = $options;
        $this->rules = static::rules($options);
    }

    /**
     * Lex source to tokens.
     *
     * @param  string $src
     * @return array
     */
    public function lex($src)
    {
        $src = preg_replace(['/\r\n|\r/m', '/\t/m'], ["\n", '    '], $src);
        $src = str_replace(['\\u00a0', '\\u2424'], [' ', "\n"], $src);

        $this->tokens = [];
        $this->tokens['links'] = [];

        return $this->token($src, true);
    }

    /**
     * Get tokens from source.
     *
     * @param  string $src
     * @return array
     */
    protected function token($src, $top = false, $bq = null)
    {
        $src = preg_replace('/^ +$/m', '', $src);

        while ($src) {

            // newline
            if (preg_match($this->rules['newline'], $src, $cap)) {

                $src = substr($src, strlen($cap[0]));

                if (strlen($cap[0]) > 1) {
                    $this->tokens[] = ['type' => 'space'];
                }
            }

            // code
            if (preg_match($this->rules['code'], $src, $cap)) {

                $src = substr($src, strlen($cap[0]));
                $cap = preg_replace('/^ {4}/m', '', $cap[0]);

                $this->tokens[] = [
                    'type' => 'code',
                    'text' => !$this->options['pedantic'] ? preg_replace('/\n+$/','',$cap) : $cap
                ];

                continue;
            }

            // fences (gfm)
            if (preg_match($this->rules['fences'], $src, $cap)) {

                $src = substr($src, strlen($cap[0]));

                $this->tokens[] = [
                    'type' => 'code',
                    'lang' => $cap[2],
                    'text' => $cap[3]
                ];

                continue;
            }

            // heading
            if (preg_match($this->rules['heading'], $src, $cap)) {

                $src = substr($src, strlen($cap[0]));

                $this->tokens[] = [
                    'type'  => 'heading',
                    'depth' => strlen($cap[1]),
                    'text'  => $cap[2]
                ];

                continue;
            }

            // table no leading pipe (gfm)
            if ($top && (preg_match($this->rules['nptable'], $src, $cap))) {

                $src = substr($src, strlen($cap[0]));

                $item = [
                    'type'   => 'table',
                    'header' => preg_split('/ *\| */', preg_replace('/^ *| *\| *$/m', '', $cap[1])),
                    'align'  => preg_split('/ *\| */', preg_replace('/^ *|\| *$/m', '', $cap[2])),
                    'cells'  => preg_split('/\n/', preg_replace('/\n$/', '', $cap[3]))
                ];

                for ($i = 0; $i < count($item['align']); $i++) {
                    if (preg_match('/^ *-+: *$/' ,$item['align'][$i])) {
                        $item['align'][$i] = 'right';
                    } elseif (preg_match('/^ *:-+: *$/' ,$item['align'][$i])) {
                        $item['align'][$i] = 'center';
                    } elseif (preg_match('/^ *:-+ *$/' ,$item['align'][$i])) {
                        $item['align'][$i] = 'left';
                    } else {
                        $item['align'][$i] = null;
                    }
                }

                for ($i = 0; $i < count($item['cells']); $i++) {
                    $item['cells'][$i] = preg_split('/ *\| */', $item['cells'][$i]);
                }

                $this->tokens[] = $item;

                continue;
            }

            // lheading
            if (preg_match($this->rules['lheading'], $src, $cap)) {

                $src = substr($src, strlen($cap[0]));

                $this->tokens[] = [
                    'type'  => 'heading',
                    'depth' => $cap[2] === '=' ? 1 : 2,
                    'text'  => $cap[1]
                ];

                continue;
            }

            // hr
            if (preg_match($this->rules['hr'], $src, $cap)) {

                $src = substr($src, strlen($cap[0]));

                $this->tokens[] = ['type' => 'hr'];

                continue;
            }

            // blockquote
            if (preg_match($this->rules['blockquote'], $src, $cap)) {

                $src = substr($src, strlen($cap[0]));

                $this->tokens[] = ['type' => 'blockquote_start'];

                $cap = preg_replace('/^ *> ?/m', '', $cap[0]);

                // Pass `top` to keep the current
                // "toplevel" state. This is exactly
                // how markdown.pl works.
                $this->token($cap, $top, true);

                $this->tokens[] = ['type' => 'blockquote_end'];

                continue;
            }

            // list
            if (preg_match($this->rules['list'], $src, $cap)) {

                $src  = substr($src, strlen($cap[0]));
                $list = $cap[0];
                $bull = $cap[2];

                $this->tokens[] = [
                    'type' => 'list_start',
                    'ordered' => strlen($bull) > 1
                ];

                // Get each top-level item.
                preg_match_all($this->rules['item'], $list, $cap);

                $next = false;
                $cap  = $cap[0];
                $l    = count($cap);

                for ($i = 0; $i < $l; $i++) {

                    $item = $cap[$i];

                    // Remove the list item's bullet
                    // so it is seen as the next token.
                    $space = strlen($item);

                    $item  = preg_replace('/^ *([*+-]|\d+\.) +/', '', $item);
                    $space -= strlen($item);

                    // Outdent whatever the
                    // list item contains. Hacky.
                    if (strpos($item, "\n ")===false) {
                        $item = !$this->options['pedantic'] ? preg_replace('/^ {1,'.$space.'}/m', '', $item) : preg_replace('/^ {1,4}/m', '', $item);
                    } else {
                        $item = preg_replace('/^ {1,'.$space.'}/m', '', $item);
                    }

                    // Determine whether the next list item belongs here.
                    // Backpedal if it does not belong in this list.
                    if ($this->options['smartLists'] && $i !== $l - 1) {

                        preg_match($this->rules['bullet'], $cap[$i + 1], $b);

                        $b = $b[0];

                        if ($bull !== $b && !(count($bull) > 1 && count($b) > 1)) {

                            $src = implode("\n", array_slice($cap, $i + 1)) + $src;
                            $i = $l - 1;
                        }
                    }

                    // Determine whether item is loose or not.
                    // Use: /(^|\n)(?! )[^\n]+\n\n(?!\s*$)/
                    // for discount behavior.
                    $loose = $next ? $next : preg_match('/\n\n(?!\s*$)/', $item);

                    if ($i !== $l - 1) {

                        $next = @$item[strlen($item) - 1] === "\n";
                        if (!$loose) $loose = $next;
                    }

                    $this->tokens[] = [
                        'type' => $loose ? 'loose_item_start' : 'list_item_start'
                    ];

                    // Recurse.
                    $this->token($item, false, $bq);

                    $this->tokens[] = ['type' => 'list_item_end'];
                }

                $this->tokens[] = ['type' => 'list_end'];

                continue;
            }


            // html
            if (preg_match($this->rules['html'], $src, $cap)) {

                $src = substr($src, strlen($cap[0]));

                $this->tokens[] = [
                    'type' => $this->options['sanitize'] ? 'paragraph' : 'html',
                    'pre'  => isset($cap[1]) && ($cap[1] === 'pre' || $cap[1] === 'script' || $cap[1] === 'style'),
                    'text' => $cap[0]
                ];

                continue;
            }

            // def
            if ((!$bq && $top) && (preg_match($this->rules['def'], $src, $cap))) {

                $src = substr($src, strlen($cap[0]));

                $this->tokens['links'][strtolower($cap[1])] = [
                    "href"  => @$cap[2],
                    "title" => @$cap[3]
                ];

                continue;
            }

            // table (gfm)
            if ($top && (preg_match($this->rules['table'], $src, $cap))) {

                $src = substr($src, strlen($cap[0]));

                $item = [
                    'type'   => 'table',
                    'header' => preg_split('/ *\| */', preg_replace('/^ *| *\| *$/m', '', $cap[1])),
                    'align'  => preg_split('/ *\| */', preg_replace('/^ *|\| *$/m', '', $cap[2])),
                    'cells'  => preg_split('/\n/', preg_replace('/\n$/', '', $cap[3]))
                ];

                for ($i = 0; $i < count($item['align']); $i++) {
                    if (preg_match('/^ *-+: *$/' ,$item['align'][$i])) {
                        $item['align'][$i] = 'right';
                    } elseif (preg_match('/^ *:-+: *$/' ,$item['align'][$i])) {
                        $item['align'][$i] = 'center';
                    } elseif (preg_match('/^ *:-+ *$/' ,$item['align'][$i])) {
                        $item['align'][$i] = 'left';
                    } else {
                        $item['align'][$i] = null;
                    }
                }

                for ($i = 0; $i < count($item['cells']); $i++) {
                    $item['cells'][$i] = preg_split('/ *\| */', preg_replace('/^ *\| *| *\| *$/m', '', $item['cells'][$i]));
                }

                $this->tokens[] = $item;

                continue;
            }

            // top-level paragraph
            if ($top && (preg_match($this->rules['paragraph'], $src, $cap))) {

                $src = substr($src, strlen($cap[0]));

                $this->tokens[] = [
                    'type' => 'paragraph',
                    'text' => preg_match('/\n$/', $cap[1]) ? substr($cap[1], 0, -1) : $cap[1]
                ];

                continue;
            }

            // text
            if (preg_match($this->rules['text'], $src, $cap)) {

                // Top-level should never reach here.
                $src = substr($src, strlen($cap[0]));

                $this->tokens[] = [
                    'type' => 'text',
                    'text' => $cap[0]
                ];

                continue;
            }

            if ($src) {
                throw new \Exception('Infinite loop on byte: ' + ord(substr($src, 0)));
            }
        }

        return $this->tokens;
    }

    /**
     * Get block grammar rules for given options.
     *
     * @param  array $options
     * @return array
     */
    protected static function rules(array $options)
    {
        if (!static::$blocks) {

            $blocks = [];

            // normal
            $blocks['normal'] = [
                'blockquote'  => '/^( *>[^\n]+(\n(?! *\[([^\]]+)\]: *<?([^\s>]+)>?(?: +["(]([^\n]+)[")])? *(?:\n+|$))[^\n]+)*\n*)+/',
                'bullet'      => '/(?:[*+-]|\d+\.)/',
                'code'        => '/^( {4}[^\n]+\n*)+/',
                'def'         => '/^ *\[([^\]]+)\]: *<?([^\s>]+)>?(?: +["(]([^\n]+)[")])? *(?:\n+|$)/',
                'fences'      => '/nooooop/',
                'heading'     => '/^ *(#{1,6}) *([^\n]+?) *#* *(?:\n+|$)/',
                'hr'          => '/^( *[-*_]){3,} *(?:\n+|$)/',
                'html'        => '/^ *(?:<!--[\s\S]*?-->|<((?!(?:a|em|strong|small|s|cite|q|dfn|abbr|data|time|code|var|samp|kbd|sub|sup|i|b|u|mark|ruby|rt|rp|bdi|bdo|span|br|wbr|ins|del|img)\b)\w+(?!:\/|[^\w\s@]*@)\b)[\s\S]+?<\/\1>|<(?!(?:a|em|strong|small|s|cite|q|dfn|abbr|data|time|code|var|samp|kbd|sub|sup|i|b|u|mark|ruby|rt|rp|bdi|bdo|span|br|wbr|ins|del|img)\b)\w+(?!:\/|[^\w\s@]*@)\b(?:"[^"]*"|\'[^\']*\'|[^\'">])*?>) *(?:\n{2,}|\s*$)/',
                'item'        => '/^( *)((?:[*+-]|\d+\.)) [^\n]*(?:\n(?!\1(?:[*+-]|\d+\.) )[^\n]*)*$/m',
                'lheading'    => '/^([^\n]+)\n *(=|-){2,} *(?:\n+|$)/',
                'list'        => '/^( *)((?:[*+-]|\d+\.)) [\s\S]+?(?:\n+(?=\1?(?:[-*_] *){3,}(?:\n+|$))|\n+(?= *\[([^\]]+)\]: *<?([^\s>]+)>?(?: +["(]([^\n]+)[")])? *(?:\n+|$))|\n{2,}(?! )(?!\1(?:[*+-]|\d+\.) )\n*|\s*$)/',
                'newline'     => '/^\n+/',
                'nptable'     => '/nooooop/',
                'paragraph'   => '/^((?:[^\n]+\n?(?!( *[-*_]){3,} *(?:\n+|$)| *(#{1,6}) *([^\n]+?) *#* *(?:\n+|$)|([^\n]+)\n *(=|-){2,} *(?:\n+|$)|( *>[^\n]+(\n(?! *\[([^\]]+)\]: *<?([^\s>]+)>?(?: +["(]([^\n]+)[")])? *(?:\n+|$))[^\n]+)*\n*)+|<(?!(?:a|em|strong|small|s|cite|q|dfn|abbr|data|time|code|var|samp|kbd|sub|sup|i|b|u|mark|ruby|rt|rp|bdi|bdo|span|br|wbr|ins|del|img)\b)\w+(?!:\/|[^\w\s@]*@)\b| *\[([^\]]+)\]: *<?([^\s>]+)>?(?: +["(]([^\n]+)[")])? *(?:\n+|$)))+)\n*/',
                'table'       => '/nooooop/',
                'text'        => '/^[^\n]+/'
            ];

            // github flavored markdown
            $blocks['gfm'] = array_merge($blocks['normal'], [
                'fences'    => '/^ *(`{3,}|~{3,}) *(\S+)? *\n([\s\S]+?)\s*\1 *(?:\n+|$)/',
                'paragraph' => '/^((?:[^\n]+\n?(?! *(`{3,}|~{3,}) *(\S+)? *\n([\s\S]+?)\s*\2 *(?:\n+|$)|( *)((?:[*+-]|\d+\.)) [\s\S]+?(?:\n+(?=\3?(?:[-*_] *){3,}(?:\n+|$))|\n+(?= *\[([^\]]+)\]: *<?([^\s>]+)>?(?: +["(]([^\n]+)[")])? *(?:\n+|$))|\n{2,}(?! )(?!\1(?:[*+-]|\d+\.) )\n*|\s*$)|( *[-*_]){3,} *(?:\n+|$)| *(#{1,6}) *([^\n]+?) *#* *(?:\n+|$)|([^\n]+)\n *(=|-){2,} *(?:\n+|$)|( *>[^\n]+(\n(?! *\[([^\]]+)\]: *<?([^\s>]+)>?(?: +["(]([^\n]+)[")])? *(?:\n+|$))[^\n]+)*\n*)+|<(?!(?:a|em|strong|small|s|cite|q|dfn|abbr|data|time|code|var|samp|kbd|sub|sup|i|b|u|mark|ruby|rt|rp|bdi|bdo|span|br|wbr|ins|del|img)\b)\w+(?!:\/|[^\w\s@]*@)\b| *\[([^\]]+)\]: *<?([^\s>]+)>?(?: +["(]([^\n]+)[")])? *(?:\n+|$)))+)\n*/'
            ]);

            // github flavored markdown + tables
            $blocks['tables'] = array_merge($blocks['gfm'], [
                'nptable' => '/^ *(\S.*\|.*)\n *([-:]+ *\|[-| :]*)\n((?:.*\|.*(?:\n|$))*)\n*/',
                'table'   => '/^ *\|(.+)\n *\|( *[-:]+[-| :]*)\n((?: *\|.*(?:\n|$))*)\n*/'
            ]);

            static::$blocks = $blocks;
        }

        $rules = static::$blocks['normal'];

        if ($options['gfm']) {
            if ($options['tables']) {
                $rules = static::$blocks['tables'];
            } else {
                $rules = static::$blocks['gfm'];
            }
        }

        return $rules;
    }
}
