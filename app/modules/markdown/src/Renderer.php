<?php

namespace Pagekit\Markdown;

class Renderer
{
    protected $options = [];

    public function init(array $options = [])
    {
        $this->options = $options;
    }

    public function code($code, $lang = null, $escaped = null)
    {
        if ($this->options['highlight']) {

            $out = $this->options['highlight']($code, $lang);

            if ($out != null && $out !== $code) {
                $escaped = true;
                $code    = $out;
            }
        }

        $class = $lang ? ' class="'.$this->options['langPrefix'].Markdown::escape($lang, true).'"' : '';
        $code  = $escaped ? $code : Markdown::escape($code, true);

        return "<pre><code{$class}>{$code}\n</code></pre>\n";
    }

    public function blockquote($quote)
    {
        return "<blockquote>\n{$quote}</blockquote>\n";
    }

    public function html($html)
    {
        return $html;
    }

    public function heading($text, $level, $raw = '')
    {
        $id = $this->options['headerPrefix'].preg_replace('/[^\w]+/m', '-', strtolower($raw));

        return "<h{$level} id=\"{$id}\">{$text}</h{$level}>\n";
    }

    public function hr()
    {
        return $this->options['xhtml'] ? "<hr/>\n" : "<hr>\n";
    }

    public function lst($body, $ordered = false)
    {
        return $ordered ? "<ol>\n{$body}</ol>\n" : "<ul>\n{$body}</ul>\n";
    }

    public function listitem($text)
    {
        return "<li>{$text}</li>\n";
    }

    public function paragraph($text)
    {
        return "<p>{$text}</p>\n";
    }

    public function table($header, $body)
    {
        return "<table>\n<thead>\n{$header}</thead>\n<tbody>\n{$body}</tbody>\n</table>\n";
    }

    public function tablerow($content)
    {
        return "<tr>\n".$content."</tr>\n";
    }

    public function tablecell($content, array $flags = [])
    {
        $type = $flags['header'] ? 'th' : 'td';
        $tag = $flags['align']
          ? '<'.$type.' style="text-align:'.$flags['align'].'">'
          : '<'.$type.'>';

        return $tag.$content."</".$type.">\n";
    }

    // span level renderer
    public function strong($text)
    {
        return "<strong>{$text}</strong>";
    }

    public function em($text)
    {
        return "<em>{$text}</em>";
    }

    public function codespan($text)
    {
        return "<code>{$text}</code>";
    }

    public function br()
    {
        return $this->options['xhtml'] ? '<br/>' : '<br>';
    }

    public function del($text)
    {
        return "<del>{$text}</del>";
    }

    public function link($href = '', $title = '', $text = '')
    {
        if ($this->options['sanitize'] && strpos($href, 'javascript:') === 0) {
            return '';
        }

        $title = $title ? " title=\"{$title}\"" : '';

        return "<a href=\"{$href}\"{$title}>{$text}</a>";
    }

    public function image($href = '', $title = '', $text= '')
    {
        $title = $title ? " title=\"{$title}\"" : '';
        $close = $this->options['xhtml'] ? '/>' : '>';

        return "<img src=\"{$href}\" alt=\"{$text}\"{$title}{$close}";
    }
}
