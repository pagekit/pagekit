<?php

namespace Pagekit\System\Link;

interface LinkInterface
{
    /**
     * Returns the types route
     *
     * @return string The types route
     */
    public function getRoute();

    /**
     * Returns the types label
     *
     * @return string The types label
     */
    public function getLabel();

    /**
     * Renders the types edit form
     */
    public function renderForm();
}
