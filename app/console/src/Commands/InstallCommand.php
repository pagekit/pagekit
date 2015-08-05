<?php

namespace Pagekit\Console\Commands;

use Pagekit\Console\Command;
use Pagekit\Console\Updater\Updater;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
        $updater = new Updater($this->config);
        $updater->run($input, $output);
    }

}
