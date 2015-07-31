<?php

namespace Pagekit\System\Model;

use Pagekit\Util\Arr;

trait DataModelTrait
{
    /** @Column(type="json_array") */
    public $data;

    /**
     * Gets a data value.
     *
     * @param  string $key
     * @param  mixed  $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return Arr::get((array) $this->data, $key, $default);
    }

    /**
     * Sets a data value.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function set($key, $value)
    {
        if (null === $this->data) {
            $this->data = [];
        }

        Arr::set($this->data, $key, $value);
    }
}
