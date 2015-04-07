<?php

$autoload = [
    'Pagekit\\Auth\\' => 'auth/src',
    'Pagekit\\Cookie\\' => 'cookie/src',
    'Pagekit\\Database\\' => 'database/src',
    'Pagekit\\Filesystem\\' => 'filesystem/src',
    'Pagekit\\Filter\\' => 'filter/src',
    'Pagekit\\Migration\\' => 'migration/src',
    'Pagekit\\Option\\' => 'option/src',
    'Pagekit\\Package\\' => 'package/src',
    'Pagekit\\Routing\\' => 'routing/src',
    'Pagekit\\Session\\' => 'session/src',
    'Pagekit\\Tree\\' => 'tree/src',
    'Pagekit\\View\\' => 'view/src'
];

$path = realpath(__DIR__.'/../../../../');
$loader = require $path.'/autoload.php';

foreach ($autoload as $namespace => $src) {
    $loader->addPsr4($namespace, $path.'/modules/'.$src);
}
