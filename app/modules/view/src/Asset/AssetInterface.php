<?php

namespace Pagekit\View\Asset;

interface AssetInterface
{
    /**
     * Gets the name.
     *
     * @return string
     */
    public function getName();

    /**
     * Gets the source.
     *
     * @return string
     */
    public function getSource();

    /**
     * Gets the path.
     *
     * @return string
     */
    public function getPath();

    /**
     * Gets the dependencies.
     *
     * @return array
     */
    public function getDependencies();

    /**
     * Gets the content.
     *
     * @return string
     */
    public function getContent();

    /**
     * Sets the content.
     *
     * @param string $content
     */
    public function setContent($content);

    /**
     * Gets all options.
     *
     * @return array
     */
    public function getOptions();

    /**
     * Gets a option.
     *
     * @param  string $name
     * @return mixed
     */
    public function getOption($name);

    /**
     * Sets a option.
     *
     * @param string $name
     * @param mixed $value
     */
    public function setOption($name, $value);

    /**
     * Gets the unique hash.
     *
     * @param  string $salt
     * @return string
     */
    public function hash($salt = '');

    /**
     * Applies filters and returns the asset as a string.
     *
     * @param  array $filters
     * @return string
     */
    public function dump(array $filters = []);
}
