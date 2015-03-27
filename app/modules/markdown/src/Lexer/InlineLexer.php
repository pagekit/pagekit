<?php

namespace Pagekit\Markdown\Lexer;

use Pagekit\Markdown\Markdown;

class InlineLexer
{
    protected $links;
    protected $inLink;
    protected $rules;
    protected $renderer;
    protected $options;

    protected static $inlines;

    /**
     * Constructor.
     *
     * @param array $links
     * @param array $options
     */
    public function __construct($links, array $options = [])
    {
        $this->links = $links;
        $this->options = $options;
        $this->renderer = $options['renderer'];
        $this->rules = static::rules($options);
    }

    /**
     * Lexing/Compiling
     *
     * @param  string $src
     * @throws \Exception
     * @return string
     */
    public function output($src)
    {
        $out = '';

        while ($src) {

            // escape
            if (preg_match($this->rules['escape'], $src, $cap)) {

                $src  = substr($src, strlen($cap[0]));
                $out .= $cap[1];

                continue;
            }

            // autolink
            if (preg_match($this->rules['autolink'], $src, $cap)) {

                $src = substr($src, strlen($cap[0]));

                if ($cap[2] === '@') {
                    $text = $cap[1][6] === ':' ? $this->mangle(substr($cap[1], 0, 7)) : $this->mangle($cap[1]);
                    $href = $this->mangle('mailto:').$text;
                } else {
                    $text = Markdown::escape($cap[1]);
                    $href = $text;
                }

                $out .= $this->renderer->link($href, null, $text);

                continue;
            }

            // url (gfm)
            if (!$this->inLink && (preg_match($this->rules['url'], $src, $cap))) {

                $src  = substr($src, strlen($cap[0]));
                $text = Markdown::escape($cap[1]);
                $href = $text;
                $out .= $this->renderer->link($href, null, $text);

                continue;
            }

            // tag
            if (preg_match($this->rules['tag'], $src, $cap)) {

                if (!$this->inLink && preg_match('/^<a /i', $cap[0])) {
                    $this->inLink = true;
                } elseif ($this->inLink && preg_match('/^<\/a>/i', $cap[0])) {
                    $this->inLink = false;
                }

                $src  = substr($src, strlen($cap[0]));
                $out .= $this->options['sanitize'] ? Markdown::escape($cap[0]) : $cap[0];

                continue;
            }

            // link
            if (preg_match($this->rules['link'], $src, $cap)) {

                $src = substr($src, strlen($cap[0]));

                $this->inLink = true;

                $out .= $this->outputLink($cap, [
                    'href'  => @$cap[2],
                    'title' => @$cap[3]
                ]);

                $this->inLink = false;

                continue;
            }

            if ((preg_match($this->rules['reflink'], $src, $cap)) || (preg_match($this->rules['nolink'], $src, $cap))) {

                $src  = substr($src, strlen($cap[0]));
                $link = preg_replace('/\s+/m', ' ', isset($cap[2]) ? $cap[2] : $cap[1]);
                $link = isset($this->links[strtolower($link)]) ? $this->links[strtolower($link)] : null;

                if (!$link || !$link["href"]) {
                    $out .= $cap[0][0];
                    $src = substr($cap[0], 1) + $src;
                    continue;
                }

                $this->inLink = true;
                $out .= $this->outputLink($cap, $link);
                $this->inLink = false;

                continue;
            }

            // strong
            if (preg_match($this->rules['strong'], $src, $cap)) {

                $src  = substr($src, strlen($cap[0]));
                $out .= $this->renderer->strong($this->output(isset($cap[2]) ? $cap[2] : $cap[1]));

                continue;
            }

            // em
            if (preg_match($this->rules['em'], $src, $cap)) {

                $src  = substr($src, strlen($cap[0]));
                $out .= $this->renderer->em($this->output(isset($cap[2]) ? $cap[2] : $cap[1]));

                continue;
            }

            // code
            if (preg_match($this->rules['code'], $src, $cap)) {

                $src  = substr($src, strlen($cap[0]));
                $out .= $this->renderer->codespan(Markdown::escape($cap[2]));

                continue;
            }

            // br
            if (preg_match($this->rules['br'], $src, $cap)) {

                $src  = substr($src, strlen($cap[0]));
                $out .= $this->renderer->br();

                continue;
            }

            // del (gfm)
            if (preg_match($this->rules['del'], $src, $cap)) {

                $src  = substr($src, strlen($cap[0]));
                $out .= $this->renderer->del($this->output($cap[1]));

                continue;
            }

            // text
            if (preg_match($this->rules['text'], $src, $cap)) {

                $src  = substr($src, strlen($cap[0]));
                $out .= Markdown::escape($this->smartypants($cap[0]));

                continue;
            }

            if ($src) {
                throw new \Exception('Infinite loop on byte: ' + ord(substr($src,0)));
            }
        }

        return $out;
    }

    /**
     * Compile link.
     *
     * @param  array $cap
     * @param  array $link
     * @return string
     */
    protected function outputLink($cap, $link)
    {
        $href  = Markdown::escape($link['href']);
        $title = $link['title'] ? Markdown::escape($link['title']) : null;

        return $cap[0][0] !== '!' ? $this->renderer->link($href, $title, $this->output($cap[1])) : $this->renderer->image($href, $title, Markdown::escape($cap[1]));
    }

    /**
     * Smartypants transformations.
     *
     * @param  string $text
     * @return string
     */
    protected function smartypants($text)
    {
        if (!$this->options['smartypants']) {
            return $text;
        }

        // em-dashes
        $text = str_replace('--', '&mdash;', $text);

        // opening singles
        $text = preg_replace('/(^|[-—\/(\[\{"\s])\'/m', '&mdash;', $text);

        // closing singles & apostrophes
        $text = str_replace('\'', '&rsquo;', $text);

        // opening doubles
        $text = preg_replace('/(^|[-—\/(\[\{‘\s])"/m', '$1&ldquo;', $text);

        // closing doubles
        $text = str_replace('"', '&rdquo;', $text);

        // opening doubles
        $text = preg_replace('/\.{3}/m', '&hellip;', $text);

        return $text;
    }

    /**
     * Mangle links.
     *
     * @param  string $text
     * @return string
     */
    protected function mangle($text)
    {
        $out = '';

        for ($i = 0; $i < strlen($text); $i++) {

            $ch = ord(substr($text, $i));

            if (rand() > 0.5) {
                $ch = 'x'.base_convert($ch, 10, 16);
            }

            $out .= '&#'.$ch.';';
        }

        return $out;
    }

    /**
     * Get inline grammar rules for given options.
     *
     * @param  array $options
     * @return array
     */
    protected static function rules(array $options)
    {
        if (!static::$inlines) {

            $inlines = [];

            // normal
            $inlines['normal'] = [
                '_href'      => '/\s*<?([\s\S]*?)>?(?:\s+[\'"]([\s\S]*?)[\'"])?\s*/',
                '_inside'    => '/(?:\[[^\]]*\]|[^\[\]]|\](?=[^\[]*\]))*/',
                'autolink'   => '/^<([^ >]+(@|:\/)[^ >]+)>/',
                'br'         => '/^ {2,}\n(?!\s*$)/',
                'code'       => '/^(`+)\s*([\s\S]*?[^`])\s*\1(?!`)/',
                'del'        => '/nooooop/',
                'em'         => '/^\b_((?:__|[\s\S])+?)_\b|^\*((?:\*\*|[\s\S])+?)\*(?!\*)/',
                'escape'     => '/^\\\([\\`*{}\[\]()#+\-.!_>])/',
                'link'       => '/^!?\[((?:\[[^\]]*\]|[^\[\]]|\](?=[^\[]*\]))*)\]\(\s*<?([\s\S]*?)>?(?:\s+[\'"]([\s\S]*?)[\'"])?\s*\)/',
                'nolink'     => '/^!?\[((?:\[[^\]]*\]|[^\[\]])*)\]/',
                'reflink'    => '/^!?\[((?:\[[^\]]*\]|[^\[\]]|\](?=[^\[]*\]))*)\]\s*\[([^\]]*)\]/',
                'strong'     => '/^__([\s\S]+?)__(?!_)|^\*\*([\s\S]+?)\*\*(?!\*)/',
                'tag'        => '/^<!--[\s\S]*?-->|^<\/?\w+(?:"[^"]*"|\'[^\']*\'|[^\'">])*?>/',
                'text'       => '/^[\s\S]+?(?=[\\<!\[_*`]| {2,}\n|$)/',
                'url'        => '/nooooop/'
            ];

            // pedantic
            $inlines['pedantic'] = array_merge($inlines['normal'], [
                'strong' => '/^__(?=\S)([\s\S]*?\S)__(?!_)|^\*\*(?=\S)([\s\S]*?\S)\*\*(?!\*)/',
                'em'     => '/^_(?=\S)([\s\S]*?\S)_(?!_)|^\*(?=\S)([\s\S]*?\S)\*(?!\*)/'
            ]);

            // github flavored markdown
            $inlines['gfm'] = array_merge($inlines['normal'], [
                'escape' => '/^\\\([\\`*{}\[\]()#+\-.!_>~|])/',
                'url'    => '/^(https?:\/\/[^\s<]+[^<.,:;"\')\]\s])/',
                'del'    => '/^~~(?=\S)([\s\S]*?\S)~~/',
                'text'   => '/^[\s\S]+?(?=[\\<!\[_*`~]|https?:\/\/| {2,}\n|$)/'
            ]);

            // github flavored markdown + line breaks
            $inlines['breaks'] = array_merge($inlines['gfm'], [
                'br'   => '/^ *\n(?!\s*$)/',
                'text' => '/^[\s\S]+?(?=[\\<!\[_*`~]|https?:\/\/| *\n|$)/'
            ]);

            static::$inlines = $inlines;
        }

        $rules = static::$inlines['normal'];

        if ($options['gfm']) {
            if ($options['breaks']) {
                $rules = static::$inlines['breaks'];
            } else {
                $rules = static::$inlines['gfm'];
            }
        } elseif ($options['pedantic']) {
            $rules = static::$inlines['pedantic'];
        }

        return $rules;
    }
}
