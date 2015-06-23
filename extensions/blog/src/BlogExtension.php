<?php

namespace Pagekit\Blog;

use Pagekit\Application as App;
use Pagekit\Module\Module;

class BlogExtension extends Module
{
    public function getPermalink()
    {
        $permalink = $this->config('permalink.type');

        if ($permalink == 'custom') {
            $permalink = $this->config('permalink.custom');
        }

        return $permalink;
    }
}
