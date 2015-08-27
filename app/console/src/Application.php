<?php

namespace Pagekit\Console;

use Pagekit\Application as App;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Command\Command as BaseCommand;

class Application extends BaseApplication
{
    /**
     * The Pagekit config.
     *
     * @var array
     */
    protected $config;

    /**
     * Constructor.
     *
     * @param array $config
     * @param string $name
     * @param string $version
     */
    public function __construct(array $config, $name = 'UNKNOWN', $version = 'UNKNOWN')
    {
        parent::__construct($name, $version);

        $this->config = $config;
    }

    /**
     * Add a command to the console.
     *
     * @param  BaseCommand $command
     * @return BaseCommand
     */
    public function add(BaseCommand $command)
    {
        if ($command instanceof Command) {
            $command->setConfig($this->config);
        }

        return parent::add($command);
    }
}
