<?php

namespace Pagekit\View\Helper;

use Pagekit\View\ViewInterface;

class DataHelper implements HelperInterface
{
    /**
     * @var array
     */
    protected $data = [];

    /**
     * Constructor.
     *
     * @param ViewInterface $view
     */
    public function __construct(ViewInterface $view)
    {
        $view->on('head', function ($event) {
            $event->setResult($event->getResult().$this->render());
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
            $output .= sprintf("        <script>var %s = %s;</script>\n", $name, json_encode($value));
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
