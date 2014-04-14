<?php

namespace Pagekit\Content;

use Pagekit\Content\Event\ContentEvent;
use Pagekit\Framework\ApplicationAware;

class ContentHelper extends ApplicationAware
{
    /**
     * Applies content plugins
     *
     * @param  string $content
     * @param  array  $parameters
     * @return mixed
     */
    public function applyPlugins($content, $parameters = array())
    {
        return $this('events')->trigger('content.plugins', new ContentEvent($content, $parameters))->getContent();
    }
}
