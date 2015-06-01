<?php

namespace Pagekit\Routing\Annotation;

use Symfony\Component\Routing\Annotation\Route as BaseRoute;

/**
 * @Annotation
 */
class Route extends BaseRoute
{
    /**
     * {@inheritdoc}
     */
    public function __construct(array $data = [])
    {
        if (isset($data['value'])) {
            $data['path'] = $data['value'];
            unset($data['value']);
        }

        foreach ($data as $key => $value) {
            $method = 'set'.str_replace('_', '', $key);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }
}
