<?php

$autoload = [
    'Pagekit\\Auth\\' => '/modules/auth/src',
    'Pagekit\\Cookie\\' => '/modules/cookie/src',
    'Pagekit\\Database\\' => '/modules/database/src',
    'Pagekit\\Filesystem\\' => '/modules/filesystem/src',
    'Pagekit\\Filter\\' => '/modules/filter/src',
    'Pagekit\\Migration\\' => '/modules/migration/src',
    'Pagekit\\Option\\' => '/system/modules/option/src',
    'Pagekit\\Package\\' => '/modules/package/src',
    'Pagekit\\Routing\\' => '/modules/routing/src',
    'Pagekit\\Session\\' => '/modules/session/src',
    'Pagekit\\Tree\\' => '/modules/tree/src',
    'Pagekit\\View\\' => '/modules/view/src'
];

$path = realpath(__DIR__.'/../../../../');
$loader = require $path.'/autoload.php';

foreach ($autoload as $namespace => $src) {
    $loader->addPsr4($namespace, $path.$src);
}
