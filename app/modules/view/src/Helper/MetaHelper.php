<?php

namespace Pagekit\View\Helper;

use Pagekit\View\View;

class MetaHelper implements HelperInterface, \IteratorAggregate
{
    /**
     * @var array
     */
    protected $metas = [];

    /**
     * {@inheritdoc}
     */
    public function register(View $view)
    {
        $view->on('head', function ($event) use ($view) {
            $view->trigger('meta', [$this]);
            $event->addResult($this->render());
        }, 20);
    }

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
        if ($value) {
            $this->metas[$name] = $value;
        }

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

        foreach ($this->metas as $name => $value) {

            if (preg_match('/^link:?/i', $name)) {

                if (!isset($value['rel'])) {
                    $value['rel'] = substr($name, 5);
                }

                $attributes = '';
                foreach ($value as $attr => $val) {
                    $attributes .= sprintf(' %s="%s"', $attr, htmlspecialchars($val));
                }
                $output .= sprintf("        <link%s>\n", $attributes);

            } else {

                $value = htmlspecialchars($value);

                if ($name == 'title') {
                    $output .= sprintf("        <title>%s</title>\n", $value);
                } else if ($name == 'base') {
                    $output .= sprintf("        <base href=\"%s\">\n", $value);
                } else if ($name == 'canonical') {
                    $output .= sprintf("        <link rel=\"%s\" href=\"%s\">\n", $name, $value);
                } elseif (preg_match('/^(og|twitter|article):/i', $name)) {
                    $output .= sprintf("        <meta property=\"%s\" content=\"%s\">\n", $name, $value);
                } else {
                    $output .= sprintf("        <meta name=\"%s\" content=\"%s\">\n", $name, $value);
                }
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

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'meta';
    }
}
