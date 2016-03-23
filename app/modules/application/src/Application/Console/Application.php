<?php

namespace Pagekit\Application\Console;

use Pagekit\Application as Container;
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
        parent::__construct($name, $version);

        $this->container = $container;

        if (isset($container['events'])) {
            $container['events']->trigger('console.init', [$this]);
        }
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
