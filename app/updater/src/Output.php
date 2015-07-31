<?php

namespace Pagekit\Updater;

use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Output extends BufferedOutput implements ConsoleOutputInterface
{
    protected $stderr;

    public function __construct()
    {
        parent::__construct();
        $this->stderr = new BufferedOutput();
    }

    public function getErrorOutput()
    {
        return $this->stderr;
    }

    public function setErrorOutput(OutputInterface $error)
    {
        $this->stderr = $error;
    }
}