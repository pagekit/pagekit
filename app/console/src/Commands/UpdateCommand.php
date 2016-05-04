<?php

namespace Pagekit\Console\Commands;

use Pagekit\Application\Console\Command;
use Pagekit\Installer\Helper\Composer;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'update';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Updates dependencies of Pagekit packages';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->addArgument('packages', InputArgument::IS_ARRAY | InputArgument::REQUIRED, '[Package name]');
        $this->addOption('prefer-source', null, InputOption::VALUE_NONE, 'Forces installation from package sources when possible, including VCS information.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $packages = [];

        foreach ((array) $this->argument('packages') as $package) {

            $path = $this->container->get('path.packages') . '/' . $package . '/composer.json';
            if (file_exists($path)) {
                $info = json_decode(file_get_contents($path), true);
            }

            if (isset($info['require']) && is_array($info['require'])) {
                $packages = array_merge($packages, $info['require']);
            }

        }

        $config = [];
        foreach (['path.temp', 'path.cache', 'path.vendor', 'path.artifact', 'path.packages', 'system.api'] as $key) {
            $config[$key] = $this->container->get($key);
        }

        $composer = new Composer($config, $output);
        $composer->install($packages, true, false, $this->option('prefer-source'));
    }
}
