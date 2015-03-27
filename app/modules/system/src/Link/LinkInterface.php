<?php

namespace Pagekit\System\Link;

interface LinkInterface
{
    /**
     * Returns the type's id
     *
     * @return string
     */
    public function getId();

    /**
     * Returns the type's label
     *
     * @return string
     */
    public function getLabel();

    /**
     * Returns true if route is handled by this link type
     *
     * @param  string $route
     * @return bool
     */
    public function accept($route);

    /**
     * Renders the type's edit form
     * @param string $link
     * @param array  $params
     * @param string $context
     */
    public function renderForm($link, $params = [], $context = '');
}
