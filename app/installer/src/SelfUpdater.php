<?php

namespace Pagekit\Installer;

use Composer\Console\HtmlOutputFormatter;
use Pagekit\Application as App;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;

class SelfUpdater
{
    /**
     * @var array
     */
    protected $cleanFolder = ['app'];

    /**
     * @var array
     */
    protected $ignoreFolder = ['packages', 'storage'];

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $output;

    /**
     * Constructor.
     *
     * @param mixed  $output
     */
    public function __construct(OutputInterface $output = null)
    {
        $this->output = $output ?: new StreamOutput(fopen('php://output', 'w'));

        if (PHP_SAPI != 'cli') {

            ob_implicit_flush(true);
            @ob_end_flush();

            $this->output->setFormatter(new HtmlOutputFormatter());
        }
    }

    /**
     * Runs Pagekit self update.
     *
     * @param $file
     * @throws \Exception
     */
    public function update($file)
    {
        try {
            $path = App::path();

            if (!file_exists($file)) {
                throw new \RuntimeException('File not found.');
            }

            $this->output->write('Preparing update...');
            $fileList = $this->getFileList($file);
            unset($fileList[array_search('.htaccess', $fileList)]);

            $fileList = array_values(array_filter($fileList, function ($file) {
                foreach ($this->ignoreFolder as $ignore) {
                    if(strpos($file, $ignore) === 0) {
                        return false;
                    }
                }

                return true;
            }));

            if ($this->isWritable($fileList, $path) !== true) {
                throw new \RuntimeException(array_reduce($fileList, function ($carry, $file) {
                    return $carry . sprintf("'%s' not writable\n", $file);
                }));
            }

            $requirements = include "zip://{$file}#app/installer/requirements.php";
            if ($failed = $requirements->getFailedRequirements()) {

                throw new \RuntimeException(array_reduce($failed, function ($carry, $problem) {
                    return $carry . "\n" . $problem->getHelpText();
                }));

            }

            $this->output->writeln('<info>done.</info>');
            $this->output->write('Entering update mode...');

            $this->setUpdateMode(true);
            $this->output->writeln('<info>done.</info>');

            $this->output->write('Extracting files...');
            $this->extract($file, $fileList, $path);
            $this->output->writeln('<info>done.</info>');

            $this->output->write('Removing old files...');
            foreach ($this->cleanup($fileList, $path) as $file) {
                $this->writeln(sprintf('<error>\'%s\’ could not be removed</error>', $file));
            }

            unlink($file);
            $this->output->writeln('<info>done.</info>');

            $this->output->write('Deactivating update mode...');
            $this->setUpdateMode(false);
            $this->output->writeln('<info>done.</info>');

            if (function_exists('opcache_reset')) {
                opcache_reset();
            }

        } catch (\Exception $e) {
            @unlink($file);
            throw $e;
        }

    }

    /**
     * Generates file list for given archive.
     *
     * @param $file
     * @return array
     */
    protected function getFileList($file)
    {
        $list = [];

        $zip = new \ZipArchive;
        if ($zip->open($file) === true) {

            for ($i = 0; $i < $zip->numFiles; $i++) {
                $list[] = $zip->getNameIndex($i);
            }
            $zip->close();

            return $list;
        } else {
            throw new \RuntimeException('Can not build file list.');
        }
    }

    /**
     * Checks if directory is writable.
     *
     * @param $fileList
     * @param $path
     * @return bool|array
     */
    protected function isWritable($fileList, $path)
    {
        $notWritable = [];

        if (!file_exists($path)) {
            throw new \RuntimeException(sprintf('"%s" not writable', $path));
        }

        foreach ($fileList as $file) {
            $file = $path . '/' . $file;

            while (!file_exists($file)) {
                $file = dirname($file);
            }

            if (!is_writable($file)) {
                $notWritable[] = $file;
            }
        }

        return $notWritable ?: true;
    }


    /**
     * Extracts an archive.
     *
     * @param $file
     * @param $fileList
     * @param $path
     */
    protected function extract($file, $fileList, $path)
    {
        $zip = new \ZipArchive;
        if ($zip->open($file) === true) {

            $zip->extractTo($path, $fileList);
            $zip->close();
        } else {
            throw new \RuntimeException('Package extraction failed.');
        }
    }

    /**
     * Scans directory for old files.
     *
     * @param $fileList
     * @param $path
     * @return array
     */
    protected function cleanup($fileList, $path)
    {
        $errorList = [];

        foreach ($this->cleanFolder as $dir) {
            array_merge($errorList, $this->doCleanup($fileList, $dir, $path));
        }

        return $errorList;
    }

    /**
     * @param $fileList
     * @param $dir
     * @param $path
     * @return array
     */
    protected function doCleanup($fileList, $dir, $path)
    {
        $errorList = [];

        foreach (array_diff(@scandir($path . '/' . $dir) ?: [], ['..', '.']) as $file) {
            $file = ($dir ? $dir . '/' : '') . $file;
            $realPath = $path . '/' . $file;

            if (is_dir($realPath)) {
                array_merge($errorList, $this->doCleanup($fileList, $file, $path));

                if (!in_array($file, $fileList)) {
                    @rmdir($realPath);
                }
            } else if (!in_array($file, $fileList) && !unlink($realPath)) {
                $errorList[] = $file;
            }
        }

        return $errorList;
    }

    /**
     * Toggles update mode without booting Pagekit application.
     *
     * @param $active
     */
    protected function setUpdateMode($active)
    {
        // TODO: Implement this.
    }
}
