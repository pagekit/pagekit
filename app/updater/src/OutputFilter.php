<?php

namespace Pagekit\Updater;

use Symfony\Component\Console\Output\StreamOutput;

class OutputFilter extends StreamOutput
{

    protected $errorLog = array();

    public function write($messages, $newline = false, $type = self::OUTPUT_NORMAL)
    {
        $this->parseMessage($messages);

        parent::write($messages, $newline, $type);
    }

    public function getError()
    {
        return $this->errorLog;
    }

    protected function parseMessage($message)
    {
        if (preg_match('/(?<=<error>).+(?=<\/error>)/', $message, $matches)) {
            $this->errorLog[] = $matches[0];
        }
    }
}