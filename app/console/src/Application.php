<?php

namespace Pagekit\Console;

use Pagekit\Application as App;
use Pagekit\Module\Loader\AutoLoader;
use Pagekit\Module\Loader\ConfigLoader;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Command\Command as BaseCommand;

class Application extends BaseApplication
{
    /**
     * The Pagekit config.
     *
     * @var array
     */
    protected $config;

    /**
     * The Pagekit application.
     *
     * @var App
     */
    protected $pagekit;

    /**
     * Constructor.
     *
     * @param array $config
     * @param string $name
     * @param string $version
     */
    public function __construct(array $config, $name = 'UNKNOWN', $version = 'UNKNOWN')
    {
        parent::__construct($name, $version);

        $this->config = $config;
    }

    /**
     * Add a command to the console.
     *
     * @param  BaseCommand $command
     * @return BaseCommand
     */
    public function add(BaseCommand $command)
    {
        if ($command instanceof Command) {
            $command->setConfig($this->config['values']);
        }

        return parent::add($command);
    }

    /**
     * Returns dynamically booted Pagekit application.
     *
     * @return App
     */
    public function getPagekit()
    {
        if (!$this->pagekit) {
            $this->pagekit = require __DIR__ . '/../autoload.php';
        }

        return $this->pagekit;
    }
}
