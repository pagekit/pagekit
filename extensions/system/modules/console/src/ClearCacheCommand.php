<?php

namespace Pagekit\Console;

use Pagekit\Application\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClearCacheCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'clearcache';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Clears the system cache.';

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->pagekit['system']->doClearCache();
    }
}
