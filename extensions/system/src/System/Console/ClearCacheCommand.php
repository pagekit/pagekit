<?php

namespace Pagekit\System\Console;

use Pagekit\Framework\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClearCacheCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'clearcache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes all cache files';

    /**
     * Execute the console command.
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->pagekit['system']->doClearCache();
    }
}
