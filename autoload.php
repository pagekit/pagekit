<?php

use Doctrine\Common\Annotations\AnnotationRegistry;

$loader = require $config['path.vendor'] . '/autoload.php';

if (file_exists($config['path.packages'] . '/autoload.php')) {
    $map = require $config['path.packages'] . '/composer/autoload_namespaces.php';
    foreach ($map as $namespace => $path) {
        $loader->set($namespace, $path);
    }

    $map = require $config['path.packages'] . '/composer/autoload_psr4.php';
    foreach ($map as $namespace => $path) {
        $loader->setPsr4($namespace, $path);
    }

    $classMap = require $config['path.packages'] . '/composer/autoload_classmap.php';
    if ($classMap) {
        $loader->addClassMap($classMap);
    }

    if (file_exists($config['path.packages'] . '/composer/autoload_files.php')) {
        $includeFiles = require $config['path.packages'] . '/composer/autoload_files.php';
        foreach ($includeFiles as $file) {
            composerRequire($file);
        }
    }
}

AnnotationRegistry::registerLoader([$loader, 'loadClass']);

return $loader;

function composerRequire($file)
{
    require $file;
}