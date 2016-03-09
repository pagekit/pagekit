<?php

namespace Pagekit\Console\Commands;

use Pagekit\Application\Console\Application;
use Pagekit\Application\Console\Command;
use Pagekit\Installer\Installer;
use Pagekit\Installer\Package\PackageManager;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SetupCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'setup';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Setup a Pagekit installation.';


    /**
     * {@inheritdoc}
     */
    protected function configure()
    {

        $this->addOption('adminpass', null, InputOption::VALUE_REQUIRED, 'Admin account password.');
        $this->addOption('adminmail', null, InputOption::VALUE_OPTIONAL, 'Admin account email.', $default='admin@example.com');
        $this->addOption('dbdriver',  null, InputOption::VALUE_REQUIRED, 'DB driver (sqlite or mysql). Default: mysql', $default='mysql');
        $this->addOption('dbprefix',  null, InputOption::VALUE_OPTIONAL, 'DB prefix. Default: pk_', $default='pk_');
        $this->addOption('dbhost',    null, InputOption::VALUE_OPTIONAL, 'MySQL host.', $default='localhost');
        $this->addOption('dbname',    null, InputOption::VALUE_OPTIONAL, 'MySQL database name.', $default='pagekit');
        $this->addOption('dbuser',    null, InputOption::VALUE_OPTIONAL, 'MySQL user. Default: root', $default='root');
        $this->addOption('dbpass',    null, InputOption::VALUE_OPTIONAL, 'MySQL password. Default: <empty>', $default='');
        $this->addOption('locale',    null, InputOption::VALUE_OPTIONAL, 'Locale. Default: en_US', $default='en_US');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $installer = new Installer($this->container);

        $dbDriver = $this->option('dbdriver');

        $config = [
            'locale' => $this->option('locale'),
            'database' => [
                'default' => $dbDriver,
                'connections' => [
                    $dbDriver => [
                        'dbname' => $this->option('dbname'),
                        'host' => $this->option('dbhost'),
                        'user' => $this->option('dbuser'),
                        'password' => $this->option('dbpass'),
                        'prefix' => $this->option('dbprefix')
                    ]
                ]
            ]
        ];

        $user = [
            'username' => 'admin',
            'password' => $this->option('adminpass'),
            'email' => $this->option('adminmail'),
        ];

        $options = [
            'system' => [
                'site' => ['locale' => $this->option('locale')],
                'admin' => ['locale' => $this->option('locale')]
            ],
            'system/site' => [
                'title' => 'Pagekit'
            ]
        ];

        $result = $installer->install($config, $options, $user);
        $status = $result['status'];
        $message = $result['message'];

        if($status == 'success') {
            $this->line("Done");
        } else {
            $this->error($message);
        }

    }
}
