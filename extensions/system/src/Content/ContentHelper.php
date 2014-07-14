<?php

namespace Pagekit\Content;

use Pagekit\Content\Event\ContentEvent;
use Pagekit\Framework\ApplicationTrait;

class ContentHelper implements \ArrayAccess
{
    use ApplicationTrait;

    /**
     * Applies content plugins
     *
     * @param  string $content
     * @param  array  $parameters
     * @return mixed
     */
    public function applyPlugins($content, $parameters = [])
    {
        return $this['events']->dispatch('content.plugins', new ContentEvent($content, $parameters))->getContent();
    }
}
