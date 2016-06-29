<?php

namespace Pagekit\Console\Commands;

use Pagekit\Application\Console\Command;
use Pagekit\Installer\Installer;
use Pagekit\Application as App;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Pagekit\Module\Loader\ConfigLoader;


class SetupCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'setup';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Setup a Pagekit installation';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->addOption('username', 'u', InputOption::VALUE_REQUIRED, 'Admin username', 'admin');
        $this->addOption('password', 'p', InputOption::VALUE_REQUIRED, 'Admin account password');
        $this->addOption('title', 't', InputOption::VALUE_OPTIONAL, 'Site title', 'Pagekit');
        $this->addOption('mail', 'm', InputOption::VALUE_OPTIONAL, 'Admin account email', 'admin@example.com');
        $this->addOption('db-driver', 'd', InputOption::VALUE_REQUIRED, 'DB driver (\'sqlite\' or \'mysql\')', 'sqlite');
        $this->addOption('db-prefix', null, InputOption::VALUE_OPTIONAL, 'DB prefix', 'pk_');
        $this->addOption('db-host', 'H', InputOption::VALUE_OPTIONAL, 'MySQL host');
        $this->addOption('db-name', 'N', InputOption::VALUE_OPTIONAL, 'MySQL database name');
        $this->addOption('db-user', 'U', InputOption::VALUE_OPTIONAL, 'MySQL user');
        $this->addOption('db-pass', 'P', InputOption::VALUE_OPTIONAL, 'MySQL password');
        $this->addOption('locale', 'l', InputOption::VALUE_OPTIONAL, 'Locale', 'en_GB');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        if (!in_array($this->option('db-driver'), ['mysql', 'sqlite'])) {
            $this->error("Unsupported db driver.");
            exit;
        }

        $this->line("Setting up Pagekit installation...");

        $app = $this->container;

        App::module('session')->config['storage'] = 'array';

        $app->boot();

        $app['module']->load('installer');

        $installer = new Installer($app);

        $dbDriver = $this->option('db-driver');

        $config = [
            'locale' => $this->option('locale'),
            'database' => [
                'default' => $dbDriver,
                'connections' => [
                    $dbDriver => [
                        'dbname' => $this->option('db-name'),
                        'host' => $this->option('db-host'),
                        'user' => $this->option('db-user'),
                        'password' => $this->option('db-pass'),
                        'prefix' => $this->option('db-prefix')
                    ]
                ]
            ]
        ];

        $user = [
            'username' => $this->option('username'),
            'password' => $this->option('password'),
            'email' => $this->option('mail'),
        ];

        $options = [
            'system' => [
                'site' => ['locale' => $this->option('locale')],
                'admin' => ['locale' => $this->option('locale')]
            ],
            'system/site' => [
                'title' => $this->option('title')
            ]
        ];

        $result = $installer->install($config, $options, $user);
        $status = $result['status'];
        $message = $result['message'];

        if ($status == 'success') {
            $this->line("Done");
        } else {
            $this->error($message);
        }
    }
}
