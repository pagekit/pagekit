<?php

namespace Pagekit\Console\Commands;

use Pagekit\Application\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class StartCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'start';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Starts the built-in web server';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->addOption('server', 's', InputOption::VALUE_OPTIONAL, 'Server name and port', '127.0.0.1:8080');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->line(sprintf('Pagekit %s Development Server started', $this->getApplication()->getVersion()));
        $this->line(sprintf('Listening on http://%s', $server = $this->option('server')));
        $this->line(sprintf('Document root is %s', getcwd()));
        $this->line('Press Ctrl-C to quit');

        exec("php -S $server index.php");
    }
}
