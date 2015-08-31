<?php

namespace Pagekit\Console\Commands;

use Pagekit\Application\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrationCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'migrate';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Migrates Pagekit.';

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = $this->container->config('system');

        if ($version = $this->container->migrator()->create('system:migrations', $config->get('version'))->run()) {
            $config->set('version', $version);

            $this->line(sprintf('<info>%s</info>', __('Your Pagekit database has been updated successfully.')));
        } else {
            $this->line(sprintf('<error>%s</error>', __('Your Pagekit database is already up-to-date!')));
        }
    }
}
