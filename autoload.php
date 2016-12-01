<?php

use Doctrine\Common\Annotations\AnnotationRegistry;

$loader = require $path . '/app/vendor/autoload.php';

if (file_exists($path . '/packages/autoload.php')) {
    $map = require $path . '/packages/composer/autoload_namespaces.php';
    foreach ($map as $namespace => $p) {
        $loader->set($namespace, $p);
    }

    $map = require $path . '/packages/composer/autoload_psr4.php';
    foreach ($map as $namespace => $p) {
        $loader->setPsr4($namespace, $p);
    }

    $classMap = require $path . '/packages/composer/autoload_classmap.php';
    if ($classMap) {
        $loader->addClassMap($classMap);
    }

    if (file_exists($path . '/packages/composer/autoload_files.php')) {
        $includeFiles = require $path . '/packages/composer/autoload_files.php';
        foreach ($includeFiles as $file) {
            require $file;
        }
    }
}

AnnotationRegistry::registerLoader([$loader, 'loadClass']);

return $loader;
