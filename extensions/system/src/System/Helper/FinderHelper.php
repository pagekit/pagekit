<?php

namespace Pagekit\System\Helper;

use Pagekit\Framework\ApplicationAware;

class FinderHelper extends ApplicationAware
{
    /**
     * @param  string $root
     * @param  string $mode
     * @return string
     */
    public function getToken($root, $mode)
    {
        return sha1($this->getKey($root, $mode));
    }

    /**
     * @param  string $hash
     * @param  string $root
     * @param  string $mode
     * @return bool
     */
    public function verify($hash, $root, $mode)
    {
        return sha1($this->getKey($root, $mode)) === $hash;
    }

    protected function getKey($root, $mode)
    {
        return $root.'.'.$mode.'.'.$this('session')->getId().'.'.$this('config')->get('app.key');
    }
}