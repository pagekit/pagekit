<?php

namespace Pagekit\Console\Commands;

use Pagekit\Application\Console\Command;
use Pagekit\Installer\Package\PackageManager;
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
    protected $description = 'Installs a Pagekit package.';

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
        $packages = [];

        foreach ((array)$this->argument('packages') as $argument) {
            $argument = explode(':', $argument);
            $packages[$argument[0]] = isset($argument[1]) && $argument[1] ? $argument[1] : '*';
        }

        $installer = new PackageManager($output);
        $installer->install($packages);
    }
}
