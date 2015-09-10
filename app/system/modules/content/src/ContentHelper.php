<?php

namespace Pagekit\Content;

use Pagekit\Application as App;
use Pagekit\Content\Event\ContentEvent;

class ContentHelper
{
    /**
     * Applies content plugins
     *
     * @param  string $content
     * @param  array  $parameters
     * @return mixed
     */
    public function applyPlugins($content, $parameters = [])
    {
        return App::trigger(new ContentEvent('content.plugins', $content, $parameters))->getContent();
    }
}
