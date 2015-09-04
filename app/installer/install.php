<?php

use Pagekit\Application as App;

App::config()->set('system/site', App::config('system/site')->set('frontpage', 1));
App::config()->set('theme-one', ['_menus' => ['main' => 'main', 'offcanvas' => 'main']]);

App::db()->insert('@blog_post', [
    'user_id' => 1,
    'slug' => 'hello-pagekit',
    'title' => 'Hello Pagekit',
    'status' => 2,
    'date' => date('Y-m-d H:i:s'),
    'modified' => date('Y-m-d H:i:s'),
    'content' => "Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\ntempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,\nquis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo\nconsequat. Duis aute irure dolor in reprehenderit in voluptate velit esse\ncillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non\nproident, sunt in culpa qui officia deserunt mollit anim id est laborum.",
    'comment_status' => 1,
    'data' => "{\"title\":null,\"markdown\":true}"
]);

App::db()->insert('@system_page', [
    'title' => 'Home',
    'content' => "<p>\n   Hello and welcome to the Pagekit demo content. \n</p>\n\n<p>\n    Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\n    tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,\n    quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo\n    consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse\n    cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non\n    proident, sunt in culpa qui officia deserunt mollit anim id est laborum.    \n</p>\n",
    'data' => "{\"title\":true}"
]);

App::db()->insert('@system_node', ['priority' => 1, 'status' => 1, 'title' => 'Home', 'slug' => 'home', 'path' => '/home', 'link' => '@page/1', 'type' => 'page', 'menu' => 'main', 'data' => "{\"defaults\":{\"id\":1}}"]);

App::db()->insert('@system_node', ['priority' => 2, 'status' => 1, 'title' => 'Blog', 'slug' => 'blog', 'path' => '/blog', 'link' => '@blog', 'type' => 'blog', 'menu' => 'main']);
