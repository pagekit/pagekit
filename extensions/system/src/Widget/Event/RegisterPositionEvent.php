<?php

namespace Pagekit\Widget\Event;

use Pagekit\Framework\Event\Event;

class RegisterPositionEvent extends Event
{
    /**
     * Registers a position.
     *
     * @param  string $id
     * @param  string $name
     * @param  string $description
     * @throws \InvalidArgumentException
     */
    public function register($id, $name, $description = '')
    {
        $this->parameters[$id] = compact('id', 'name', 'description');
    }

    /**
     * @return array
     */
    public function getPositions()
    {
        return $this->parameters;
    }
}