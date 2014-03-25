<?php

namespace Pagekit\System\Event;

use Pagekit\Framework\Event\Event;

class LocaleEvent extends Event
{
    /**
     * @var array
     */
    protected $messages;

    /**
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @param array
     * @param string $domain
     */
    public function setMessages(array $messages, $domain = 'messages')
    {
        $this->messages[$domain] = $messages;
    }

    /**
     * @param array
     * @param string $domain
     */
    public function addMessages(array $messages, $domain = 'messages')
    {
        if (isset($this->messages[$domain])) {
            $messages = array_merge($this->messages[$domain], $messages);
        }

        $this->messages[$domain] = $messages;
    }
}
