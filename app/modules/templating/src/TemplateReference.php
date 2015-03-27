<?php

namespace Pagekit\Templating;

use Symfony\Component\Templating\TemplateReference as BaseTemplateReference;

class TemplateReference extends BaseTemplateReference
{
    /**
     * {@inheritdoc}
     */
    public function __construct($name = null, $path = null, $engine = null)
    {
        $this->parameters = [
            'name'   => $name,
            'path'   => $path ?: $name,
            'engine' => $engine,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return $this->parameters['path'];
    }
}
