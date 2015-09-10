<?php

namespace Pagekit\Log;

use Monolog\Logger as BaseLogger;

class Logger extends BaseLogger
{
    /**
     * Log shortcut.
     *
     * @see log()
     */
    public function __invoke($level, $message, array $context = [])
    {
        return $this->log($level, $message, $context);
    }
}
