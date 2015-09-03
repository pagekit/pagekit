<?php

namespace Pagekit\Console\Commands;

use Pagekit\Application\Console\Command;
use Pagekit\Installer\Package\PackageManager;
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
        $manager = new PackageManager();

        $scripts = $manager->loadScripts(null, $this->container->path() . '/app/system/scripts.php');
        if (isset($scripts['updates'])) {
            $updates = $manager->filterUpdates($scripts['updates'], $config->get('version'));
            $manager->execute($updates);
        }

        $config->set('version', $this->container->version());
        $this->line(sprintf('<info>%s</info>', __('Your Pagekit database has been updated successfully.')));
    }
}
