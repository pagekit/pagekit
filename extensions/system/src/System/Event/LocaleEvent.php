<?php

namespace Pagekit\System\Event;

use Pagekit\Framework\Event\Event;

class LocaleEvent extends Event
{
    /**
     * @var array
     */
    protected $locale;

    /**
     * @return array
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param string $namespace
     * @param array
     */
    public function setLocale($namespace, array $values)
    {
        $this->locale[$namespace] = $values;
    }

    /**
     * @param string $namespace
     * @param array
     */
    public function addLocale($namespace, array $values)
    {
        if (isset($this->locale[$namespace])) {
            $values = array_merge($this->locale[$namespace], $values);
        }

        $this->locale[$namespace] = $values;
    }
}
