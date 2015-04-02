<?php

namespace Pagekit\View;

interface ViewInterface
{
    /**
     * Renders a view.
     *
     * @param  string $name
     * @param  array  $parameters
     * @return string
     */
    public function render($name, array $parameters = []);
}
