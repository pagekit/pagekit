<?php

use Doctrine\Common\Annotations\AnnotationRegistry;

$loader = require __DIR__ . '/app/vendor/autoload.php';

if (file_exists(__DIR__ . '/packages/autoload.php')) {
    $map = require __DIR__ . '/packages/composer/autoload_namespaces.php';
    foreach ($map as $namespace => $p) {
        $loader->set($namespace, $p);
    }

    $map = require __DIR__ . '/packages/composer/autoload_psr4.php';
    foreach ($map as $namespace => $p) {
        $loader->setPsr4($namespace, $p);
    }

    $classMap = require __DIR__ . '/packages/composer/autoload_classmap.php';
    if ($classMap) {
        $loader->addClassMap($classMap);
    }

    if (file_exists(__DIR__ . '/packages/composer/autoload_files.php')) {
        $includeFiles = require __DIR__ . '/packages/composer/autoload_files.php';
        foreach ($includeFiles as $file) {
            require $file;
        }
    }
}

AnnotationRegistry::registerLoader([$loader, 'loadClass']);

return $loader;
