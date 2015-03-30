<?php

namespace Pagekit\View\Helper;

class MetaHelper implements \IteratorAggregate
{
    /**
     * @var array
     */
    protected $metas = [];

    /**
     * Adds meta tags.
     *
     * @param  $metas
     * @return self
     */
    public function __invoke(array $metas)
    {
        foreach ($metas as $name => $value) {
            $this->add($name, $value);
        }

        return $this;
    }

    /**
     * Gets a meta tag.
     *
     * @param  string $name
     * @return string
     */
    public function get($name)
    {
        return isset($this->metas[$name]) ? $this->metas[$name] : null;
    }

    /**
     * Adds a meta tag.
     *
     * @param  string $name
     * @param  string $value
     * @return self
     */
    public function add($name, $value = '')
    {
        $this->metas[$name] = $value;

        return $this;
    }

    /**
     * Renders the meta tags.
     *
     * @return string
     */
    public function render()
    {
        $output = '';

        if (isset($this->metas['title'])) {
            $output .= sprintf("        <title>%s</title>\n", $this->metas['title']);
        }

        foreach ($this->metas as $name => $value) {

            $value = htmlspecialchars($value);

            if ($name == 'title') {
                continue;
            } elseif ($name == 'canonical') {
                $output .= sprintf("        <link rel=\"%s\" href=\"%s\">\n", $name, $value);
            } elseif (preg_match('/^(og|twitter):/i', $name)) {
                $output .= sprintf("        <meta property=\"%s\" content=\"%s\">\n", $name, $value);
            } else {
                $output .= sprintf("        <meta name=\"%s\" content=\"%s\">\n", $name, $value);
            }
        }

        return $output;
    }

    /**
     * Returns an iterator for meta tags.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->metas);
    }
}
