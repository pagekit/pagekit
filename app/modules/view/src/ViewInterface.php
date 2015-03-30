<?php

namespace Pagekit\View;

interface ViewInterface
{
    /**
     * Gets the layout template.
     *
     * @return string
     */
    public function getLayout();

    /**
     * Sets the layout template.
     *
     * @param string $layout
     */
    public function setLayout($layout);
}
