<?php

use Pagekit\Application as App;

/********************************************
 * Post install script. Insert sample data. *
 ********************************************/

// Enable blog. TODO: find a prettier way

$app = App::getInstance();
$blogpath = $app['path.extensions'].'/blog';
if ($version = $app['migrator']->create($blogpath.'/migrations')->run()) {
    $app['config']('blog')->set('version', $version);
}

App::config()->set('system', [
    'site' => [
        'theme' => 'one'
    ],
    'extensions' => ['blog']
]);

App::config()->set('system/site', [
    'menus' =>
        ['main' =>
            ['id' => 'main',
            'label' => 'Main'
        ]
    ],
    'frontpage' => 1
]);

App::config()->set('system/dashboard', [
  '55dda578e93b5' =>
  [
    'type' => 'location',
    'column' => 1,
    'idx' => 0,
    'units' => 'metric',
    'id' => '55dda578e93b5',
    'uid' => 2911298,
    'city' => 'Hamburg',
    'country' => 'DE',
    'coords' =>
    [
      'lon' => 10,
      'lat' => 53.549999,
    ],
  ],
  '55dda581d5781' =>
  [
    'type' => 'feed',
    'column' => 2,
    'idx' => 0,
    'count' => 5,
    'content' => '1',
    'id' => '55dda581d5781',
    'title' => 'Pagekit News',
    'url' => 'http://pagekit.com/blog/feed',
  ],
  '55dda6e3dd661' =>
  [
    'type' => 'user',
    'column' => 0,
    'idx' => 100,
    'show' => 'registered',
    'display' => 'list',
    'total' => '1',
    'count' => 12,
    'id' => '55dda6e3dd661',
  ],
]);

App::config()->set('one', [
    '_menus' => [
        'main' => 'main',
        'offcanvas' => 'main'
    ]
]);

$sql = <<<EOT
-- blog sample data
INSERT INTO `@blog_post` (`id`, `user_id`, `slug`, `title`, `status`, `date`, `modified`, `content`, `excerpt`, `comment_status`, `comment_count`, `data`, `roles`)
VALUES
    (1,1,'hello-pagekit','Hello Pagekit',2,'2015-08-26 11:52:04','2015-08-26 11:52:43','Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\ntempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,\nquis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo\nconsequat. Duis aute irure dolor in reprehenderit in voluptate velit esse\ncillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non\nproident, sunt in culpa qui officia deserunt mollit anim id est laborum.','',1,0,'{\"title\":null,\"markdown\":true}',NULL);

-- pages sample data
INSERT INTO `@system_page` (`id`, `title`, `content`, `data`)
VALUES
    (1,'Home','<p>\n   Hello and welcome to the Pagekit demo content. \n</p>\n\n<p>\n    Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\n    tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,\n    quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo\n    consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse\n    cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non\n    proident, sunt in culpa qui officia deserunt mollit anim id est laborum.    \n</p>\n','{\"title\":true}');

-- system nodes
INSERT INTO `@system_node` (`id`, `parent_id`, `priority`, `status`, `title`, `slug`, `path`, `link`, `type`, `menu`, `roles`, `data`)
VALUES
    (1,0,1,1,'Home','home','/home','@page/1','page','main',NULL,'{\"defaults\":{\"id\":1}}'),
    (2,0,2,1,'Blog','blog','/blog','@blog','blog','main',NULL,'[]');
EOT;

foreach (explode(';', $sql) as $query) {
    if ($query = trim($query)) {
        App::db()->executeUpdate($query);
    }
}


