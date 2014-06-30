<?php

namespace Pagekit\System\Console;

use Pagekit\Framework\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RoutesCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'routes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all registered routes';

    /**
     * Execute the console command.
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $routes = $this->pagekit['router']->getRouteCollection();

        if (count($routes) == 0) {
            $this->error("Your application doesn't have any routes.");
            return;
        }

        $rows = array();
        foreach ($routes as $name => $route) {
            $rows[$name] = array(
                'name' => $name,
                'uri' => $route->getPath(),
                'action' => is_string($ctrl = $route->getDefault('_controller')) ? $ctrl : 'Closure'
            );

            if ($this->option('verbose')) {
                $rows[$name]['admin'] = $route->getOption('admin') ? '1' : '';
                $rows[$name]['csrf'] = $route->getOption('_csrf_name') ? '1' : '';
                $rows[$name]['access'] = ($access = array_diff($route->getOption('access', array()), array('system: access admin area'))) ? json_encode($access) : '';
            }
        }

        $headers = array('Name', 'URI', 'Action');
        if ($this->option('verbose')) {
            $headers = array_merge($headers, array('Admin', 'Csrf', 'Access'));
        }

        $table = $this->getHelperSet()->get('table');
        $table->setHeaders($headers);
        $table->setRows($rows)->render($this->output);
    }
}
