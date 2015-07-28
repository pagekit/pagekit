<?php

namespace Pagekit\Console;

use Pagekit\Application\Console\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InstallCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'install';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Installs a Pagekit package';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->addArgument('packages', InputArgument::IS_ARRAY, '[Package name]:[Version constraint]');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $packages = $this->argument('packages');

        $command = sprintf('php %s/app/updater/index.php -p "%s"',
            $this->container['path'],
            implode(' ', $packages));

        exec($command);
    }

}
