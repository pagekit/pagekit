<?php

namespace Pagekit\View\Helper;

class SectionHelper
{
    protected $sections     = [];
    protected $openSections = [];
    protected $renderer     = [];
    protected $defaults     = [];

    /**
     * Adds a renderer.
     *
     * @param string   $name
     * @param callable $renderer
     */
    public function addRenderer($name, callable $renderer)
    {
        $this->renderer[$name] = $renderer;
    }

    /**
     * Registers default options for a section.
     *
     * @param string $name
     * @param array  $options
     */
    public function register($name, $options = [])
    {
        $this->defaults[$name] = $options;
    }

    /**
     * Starts a new section.
     *
     * This method starts an output buffer that will be
     * closed when the end() method is called.
     *
     * @param  string $name
     * @param  string $options
     * @throws \InvalidArgumentException
     */
    public function start($name, $options = 'overwrite')
    {
        if (in_array($name, $this->openSections)) {
            throw new \InvalidArgumentException(sprintf('A section named "%s" is already started.', $name));
        }

        if (is_string($options)) {
            $options = ['mode' => $options];
        }

        if (!isset($options['mode'])) {
            $options['mode'] = 'overwrite';
        }

        $this->openSections[] = [$name, $options];
        $this->sections[$name] = isset($this->sections[$name]) ? $this->sections[$name] : '';

        ob_start();
        ob_implicit_flush(0);
    }

    /**
     * Ends a section.
     *
     * @throws \LogicException
     */
    public function end()
    {
        if (!$this->openSections) {
            throw new \LogicException('No section started.');
        }

        list($name, $options) = array_pop($this->openSections);

        $section = ob_get_clean();

        switch($options['mode']) {
            case 'append':
                $this->sections[$name] .= $section;
                break;
            case 'prepend':
                $this->sections[$name] = $section . $this->sections[$name];
                break;
            case 'show':
                $this->sections[$name] = $section;
                $this->output($name, $options);
                break;
            default:
                $this->sections[$name] = $section;
        }
    }

    /**
     * Returns true if the section exists.
     *
     * @param  string $name
     * @return bool
     */
    public function has($name)
    {
        return isset($this->sections[$name]);
    }

    /**
     * Gets the section value.
     *
     * @param  string      $name
     * @param  bool|string $default
     * @return string The section content
     */
    public function get($name, $default = false)
    {
        return isset($this->sections[$name]) ? $this->sections[$name] : $default;
    }

    /**
     * Sets a section value.
     *
     * @param string $name
     * @param mixed  $content
     */
    public function set($name, $content)
    {
        $this->sections[$name] = is_string($content) ? $content : [$content];
    }

    /**
     * Appends to a section.
     *
     * @param string $name
     * @param mixed  $content
     */
    public function append($name, $content)
    {
        if (!isset($this->sections[$name])) {
            $this->set($name, $content);
        } elseif (is_string($this->sections[$name])) {
            $this->sections[$name] .= $content;
        } else {
            array_push($this->sections[$name], $content);
        }
    }

    /**
     * Prepends to a section.
     *
     * @param string $name
     * @param mixed  $content
     */
    public function prepend($name, $content)
    {
        if (!isset($this->sections[$name])) {
            $this->set($name, $content);
        } elseif (is_string($this->sections[$name])) {
            $this->sections[$name] = $content . $this->sections[$name];
        } else {
            array_unshift($this->sections[$name], $content);
        }
    }

    /**
     * Renders a section.
     *
     * @param  string $name
     * @param  array  $options
     * @return string
     */
    public function render($name, array $options = [])
    {
        if (!isset($this->sections[$name])) {
            return false;
        }

        $options += isset($this->defaults[$name]) ? $this->defaults[$name] : [];

        $renderer = isset($options['renderer'], $this->renderer[$options['renderer']]) ? $options['renderer'] : false;

        return $renderer ? $this->renderer[$renderer]($name, $this->sections[$name], $options) : $this->renderDefault($this->sections[$name]);
    }

    /**
     * Outputs a section.
     *
     * @param  string $name
     * @param  array  $options
     */
    public function output($name, array $options = [])
    {
        echo $this->render($name, $options);
    }

    /**
     * The default renderer.
     *
     * @param  mixed $value
     * @return string
     */
    protected function renderDefault($value)
    {
        if (is_array($value)) {
            return implode('', array_map([$this, 'renderDefault'], $value));
        }

        if (is_string($value)) {
            return $value;
        }

        if (is_object($value) && method_exists($value, '__toString')) {
            return (string) $value;
        }

        if (is_callable($value)) {
            return $value();
        }
    }
}
