<?php

namespace Pagekit\System\Console;

use Pagekit\Framework\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class StartCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Starts the built-in web server';

    /**
     * Starts the built-in web server.
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->line(sprintf('Pagekit %s Development Server started', $this->getApplication()->getVersion()));
        $this->line(sprintf('Listening on http://%s', $server = $this->option('server')));
        $this->line(sprintf('Document root is %s', getcwd()));
        $this->line('Press Ctrl-C to quit');

        exec("php -S $server pagekit.php");
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->addOption('server', 's', InputOption::VALUE_OPTIONAL, 'Server name and port', '127.0.0.1:8080');
    }
}
