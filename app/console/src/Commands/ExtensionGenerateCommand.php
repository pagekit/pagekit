<?php

namespace Pagekit\Console\Commands;

use Pagekit\Application\Console\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Finder\Finder;

class ExtensionGenerateCommand extends Command
{
    /**
    * {@inheritdoc}
    */
    protected $name = 'extension:generate';

    /**
    * {@inheritdoc}
    */
    protected $description = 'Generate a new extension';

    protected $visitor;

    /**
    * {@inheritdoc}
    */
    protected function configure()
    {
        $this->addArgument('name', InputArgument::OPTIONAL, 'Extension Name');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $options = $this->getGeneratorOptions($output);
        $this->generateTemplateFiles($options);
    }

    protected function generateTemplateFiles($options) {
        $templateDirectory = $this->container->path() . '/app/console/src/Templates/Extension';
        $outputDirectory = $this->container->path() . '/packages/' . $options['name'];


        if (is_dir($outputDirectory)) {
            // don't create the bundle if it already exists
            $this->line('The extension already exists');
            return;
        } else {
            // create the directory for the extension
            mkdir($outputDirectory, 0777, true);
            mkdir($outputDirectory . '/src/Controller', 0777, true);
        }

        $twig = new \Twig_Environment(new \Twig_Loader_Filesystem($templateDirectory), array(
            'debug' => true,
            'cache' => false,
            'strict_variables' => true,
            'autoescape' => true
        ));

        $this->line('Generating template files');

        // get the template files
        $files = $files = Finder::create()->files()->in($templateDirectory);
        $files->in($templateDirectory . '/src/Controller');
        $files = $files->name('*.twig');

        $progress = new ProgressBar($this->output, count($files));
        $progress->start();

        // fill in the templates, write to the extension directory
        foreach ($files as $file) {
            $relativeFilePath = str_replace($templateDirectory, '',  $file->getPathname());
            $destinationFilePath = $outputDirectory . '/' . str_replace('.twig', '', $relativeFilePath);

            file_put_contents($destinationFilePath, $twig->render($relativeFilePath, $options));
            $progress->advance();
        }
        $progress->finish();
        $this->line('Your extension has been created in packages/' . $options['name']);
    }

    protected function getGeneratorOptions(OutputInterface $output)
    {
        $options = array();
        $dialog = $this->getHelper('dialog');
        $options['name'] = $this->argument('name');

        if (empty($options['name'])) {
            $options['name'] = $dialog->ask(
                $output,
                '<question>Please enter the name of the extension</question> ',
                'my_extension'
            );
        }

        $options['title'] = $dialog->ask(
            $output,
            '<question>Enter a human-readable name for extension</question> ',
            'My Extension'
        );

        $options['author'] = $dialog->ask(
            $output,
            '<question>Enter your name</question> ',
            false
        );

        $options['email'] = $dialog->ask(
            $output,
            '<question>Enter your email</question> ',
            false
        );

        $options['namespace'] = $dialog->ask(
            $output,
            '<question>PHP namespace of the extension, eg Pagekit\Hello.</question> ',
            false
        );

        return $options;
    }
}
