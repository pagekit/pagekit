<?php

namespace Pagekit\Session;

use Symfony\Component\HttpFoundation\Session\Flash\AutoExpireFlashBag;

class MessageBag extends AutoExpireFlashBag
{
    /**
     * Detailed debug information
     */
    const DEBUG = 'debug';

    /**
     * Interesting events
     */
    const INFO = 'info';

    /**
     * Exceptional occurrences that are not errors
     */
    const WARNING = 'warning';

    /**
     * Runtime errors
     */
    const ERROR = 'error';

    /**
     * Success messages
     */
    const SUCCESS = 'success';

    /**
     * Constructor.
     *
     * @param string $name
     * @param string $storageKey
     */
    public function __construct($name = 'messages', $storageKey = '_pk_messages')
    {
        parent::__construct($storageKey);

        $this->setName($name);
    }

    /**
     * Adds debug message
     *
     * @param string $message
     */
    public function debug($message)
    {
        $this->add(self::DEBUG, $message);
    }

    /**
     * Adds info message
     *
     * @param string $message
     */
    public function info($message)
    {
        $this->add(self::INFO, $message);
    }

    /**
     * Adds warning message
     *
     * @param string $message
     */
    public function warning($message)
    {
        $this->add(self::WARNING, $message);
    }

    /**
     * Adds error message
     *
     * @param string $message
     */
    public function error($message)
    {
        $this->add(self::ERROR, $message);
    }

    /**
     * Adds success message
     *
     * @param string $message
     */
    public function success($message)
    {
        $this->add(self::SUCCESS, $message);
    }

    /**
     * Gets array of message levels
     *
     * @return array
     */
    public static function levels()
    {
        return [
            self::DEBUG,
            self::INFO,
            self::WARNING,
            self::ERROR,
            self::SUCCESS
        ];
    }
}
