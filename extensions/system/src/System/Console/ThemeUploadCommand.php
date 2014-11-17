<?php

namespace Pagekit\System\Console;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ThemeUploadCommand extends ExtensionUploadCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'theme:upload';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Uploads a theme to the marketplace';

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->upload($this->argument('theme'), $this->pagekit['path.themes'], 'theme.json');
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->addArgument('theme', InputArgument::REQUIRED, 'Theme name')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Force overwrite');
    }
}
