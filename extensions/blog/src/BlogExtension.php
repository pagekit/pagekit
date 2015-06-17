<?php

namespace Pagekit\Blog;

use Pagekit\Application as App;
use Pagekit\System\Extension;

class BlogExtension extends Extension
{
    public function enable()
    {
        if ($version = App::migrator()->create('blog:migrations', $this->config('version'))->run()) {
            App::config($this->name)->set('version', $version);
        }
    }

    public function uninstall()
    {
        App::migrator()->create('blog:migrations', $this->config('version'))->run(0);
        App::config()->remove($this->name);
    }

    public function getPermalink()
    {
        $permalink = $this->config('permalink.type');

        if ($permalink == 'custom') {
            $permalink = $this->config('permalink.custom');
        }

        return $permalink;
    }
}
