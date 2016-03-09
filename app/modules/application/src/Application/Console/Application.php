<?php

namespace Pagekit\Application\Console;

use Pagekit\Application as Container;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Command\Command as BaseCommand;

class Application extends BaseApplication
{
    /**
     * The Pagekit application instance.
     *
     * @var Container
     */
    protected $container;

    /**
     * Constructor.
     *
     * @param Container $container
     * @param string $name
     */
    public function __construct(Container $container, $name = 'UNKNOWN', $version = 'UNKNOWN')
    {
        $this->container = $container;
        parent::__construct($name, $version);


        if (isset($container['events'])) {
            $container['events']->trigger('console.init', [$this]);
        }
    }

    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        if (!$this->container->isBooted()) {
            $this->container->boot();
        }

        return parent::run($input, $output);
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
            $command->setContainer($this->container);
        }

        return parent::add($command);
    }
}
