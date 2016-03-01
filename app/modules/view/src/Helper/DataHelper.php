<?php

namespace Pagekit\View\Helper;

use Pagekit\View\View;

class DataHelper implements HelperInterface
{
    /**
     * @var array
     */
    protected $data = [];

    /**
     * Encode <, >, ', &, and " for RFC4627-compliant JSON, which may also be embedded into HTML.
     * 15 === JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT
     *
     * @var int
     */
    protected $encodingOptions = 15;

    /**
     * {@inheritdoc}
     */
    public function register(View $view)
    {
        $view->on('head', function ($event) use ($view) {
            $view->trigger('data', [$this]);
            $event->addResult($this->render());
        }, 10);
    }

    /**
     * Add shortcut.
     *
     * @see add()
     */
    public function __invoke($name, $value)
    {
        $this->add($name, $value);
    }

    /**
     * Gets the data values or a value by name.
     *
     * @param  null|string $name
     * @return array
     */
    public function get($name = null)
    {
        if ($name === null) {
            return $this->data;
        }

        return isset($this->data[$name]) ? $this->data[$name] : null;
    }

    /**
     * Adds a data value to an existing key name.
     *
     * @param  string $name
     * @param  mixed  $value
     * @return self
     */
    public function add($name, $value)
    {
        if (isset($this->data[$name]) && is_array($this->data[$name])) {
           $value = array_replace_recursive($this->data[$name], $value);
        }

        $this->data[$name] = $value;
    }

    /**
     * Renders the data tags.
     *
     * @return string
     */
    public function render()
    {
        $output = '';

        foreach ($this->data as $name => $value) {
            $output .= sprintf("        <script>var %s = %s;</script>\n", $name, json_encode($value, $this->encodingOptions));
        }

        return $output;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'data';
    }
}
