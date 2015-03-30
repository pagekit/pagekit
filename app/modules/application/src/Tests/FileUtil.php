<?php

namespace Pagekit\Tests;

trait FileUtil
{
    public function getTempFile($prefix = null)
    {
    	$temp = realpath(sys_get_temp_dir());

    	if ($prefix) {
			return tempnam($temp, $prefix);
    	}

		return tempnam($temp, '');
    }

    public function getTempDir($prefix = null, $mode = 0777)
    {
    	$temp = realpath(sys_get_temp_dir()).DIRECTORY_SEPARATOR;

    	if ($prefix) {
    		$temp .= $prefix;
    	}

        do {
	      	$dir = $temp.uniqid();
        } while (file_exists($dir));

  		mkdir($dir, $mode);

		return $dir;
    }

    public function removeFile($file)
    {
		return unlink($file);
    }

    public function removeDir($dir)
    {
    	if (is_dir($dir) && !is_link($dir)) {

            $iterator = new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS);

            foreach (new \RecursiveIteratorIterator($iterator, \RecursiveIteratorIterator::CHILD_FIRST) as $file) {
                if ($file->isFile()) {
                    unlink($file->getRealPath());
                } elseif ($file->isDir()) {
                    rmdir($file->getRealPath());
                }
            }

            rmdir($dir);
        }

        return !is_dir($dir);
    }

    public function mirror($originDir, $targetDir)
    {
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($originDir, \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST);

        $targetDir = rtrim($targetDir, '/\\');
        $originDir = rtrim($originDir, '/\\');

        foreach ($iterator as $file) {
            $target = str_replace($originDir, $targetDir, $file->getPathname());

            if (is_dir($file) && !is_link($file)) {
                mkdir($target);
            } elseif (is_file($file) && !is_link($file)) {
                copy($file, $target);
            } else {
                throw new IOException(sprintf('Can only copy files and directories (%s).', $file));
            }
        }
    }
}