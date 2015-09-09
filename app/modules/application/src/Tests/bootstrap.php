<?php

$autoload = [
    'Pagekit\\Auth\\' => '/app/modules/auth/src',
    'Pagekit\\Config\\' => '/app/modules/config/src',
    'Pagekit\\Cookie\\' => '/app/modules/cookie/src',
    'Pagekit\\Database\\' => '/app/modules/database/src',
    'Pagekit\\Filesystem\\' => '/app/modules/filesystem/src',
    'Pagekit\\Filter\\' => '/app/modules/filter/src',
    'Pagekit\\Migration\\' => '/app/modules/migration/src',
    'Pagekit\\Package\\' => '/app/modules/package/src',
    'Pagekit\\Routing\\' => '/app/modules/routing/src',
    'Pagekit\\Session\\' => '/app/modules/session/src',
    'Pagekit\\Tree\\' => '/app/modules/tree/src',
    'Pagekit\\View\\' => '/app/modules/view/src'
];

$path = realpath(__DIR__.'/../../../../../');
$loader = require $path.'/autoload.php';

foreach ($autoload as $namespace => $src) {
    $loader->addPsr4($namespace, $path.$src);
}
