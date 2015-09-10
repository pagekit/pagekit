<?php

namespace Pagekit\Debug;

use DebugBar\DebugBar as BaseDebugBar;

class DebugBar extends BaseDebugBar
{
    /**
     * {@inheritdoc}
     */
    public function getCurrentRequestId()
    {
        if ($this->requestId == null) {
            $this->requestId = sha1(parent::getCurrentRequestId());
        }

        return $this->requestId;
    }
}
