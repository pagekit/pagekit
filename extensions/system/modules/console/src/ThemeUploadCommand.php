<?php

namespace Pagekit\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ThemeUploadCommand extends ExtensionUploadCommand
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'theme:upload';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Uploads a theme to the marketplace';

    /**
     * @var string
     */
    protected $json = 'theme.json';

    /**
     * {@inheritdoc}
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);

        $this->path = $this->pagekit['path.themes'];
    }
}
