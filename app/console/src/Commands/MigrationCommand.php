<?php

namespace Pagekit\Console\Commands;

use Pagekit\Application\Console\Command;
use Pagekit\Installer\Package\PackageScripts;
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
    protected $description = 'Migrates Pagekit';

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = $this->container->config('system');

        $scripts = new PackageScripts($this->container->path().'/app/system/scripts.php', $config->get('version'));
        if ($scripts->hasUpdates()) {
            $scripts->update();
        }

        $config->set('version', $this->container->version());
        $this->line(sprintf('<info>%s</info>', __('Your Pagekit database has been updated successfully.')));
    }
}
