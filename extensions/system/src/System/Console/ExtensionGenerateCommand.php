<?php

namespace Pagekit\System\Console;

use Pagekit\Framework\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class ExtensionGenerateCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'extension:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates an extension skeleton files';

    /**
     * Builds an extension skeleton with minimum requirements.
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $this->argument('extension');

        if (is_dir($path = $this->pagekit['path.extensions']."/$name")) {
            $this->error("Extension already exists '$path'");
            exit;
        }

        $title     = $this->ask('Title: ');
        $author    = $this->ask('Author: ');
        $email     = $this->ask('Email: ');
        $namespace = $this->ask('PHP Namespace: ');
        $classname = ucfirst($name).'Extension';

        $replace = [
            '%NAME%'          => $name,
            '%TITLE%'         => $title,
            '%AUTHOR%'        => $author,
            '%EMAIL%'         => $email,
            '%CLASSNAME%'     => $classname,
            '%NAMESPACE%'     => $namespace,
            '%NAMESPACE_ESC%' => addslashes($namespace)
        ];

        foreach (Finder::create()->files()->in(__DIR__.'/skeleton/extension') as $file) {

            if ($file->getFilename() == 'DefaultExtension.php') {
                $filename = "$path/src/$classname.php";
            } else {
                $filename = "$path/".$file->getRelativepathname();
            }

            if (!is_dir($dir = dirname($filename))) {
                mkdir($dir, 0777, true);
            }

            $content = file_get_contents($file->getPathname());
            $content = str_replace(array_keys($replace), $replace, $content);

            file_put_contents($filename, $content);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->addArgument('extension', InputArgument::REQUIRED, 'Extension name');
    }
}
