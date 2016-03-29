<?php

use Pagekit\Application as App;

App::config()->set('system/site', App::config('system/site')->merge([
    'frontpage' => 1, 'view' => ['logo' => 'storage/pagekit-logo.svg']
]));

App::db()->insert('@system_config', ['name' => 'theme-one', 'value' => '{"logo_contrast":"storage\\/pagekit-logo-contrast.svg","_menus":{"main":"main","offcanvas":"main"},"_positions":{"hero":[1],"footer":[2]},"_widgets":{"1":{"title_hide":true,"title_size":"uk-panel-title","alignment":true,"html_class":"","panel":""},"2":{"title_hide":true,"title_size":"uk-panel-title","alignment":"true","html_class":"","panel":""}},"_nodes":{"1":{"title_hide":true,"title_large":false,"alignment":true,"html_class":"","sidebar_first":false,"hero_image":"storage\\/home-hero.jpg","hero_viewport":true,"hero_contrast":true,"navbar_transparent":true}}}']);

App::db()->insert('@system_node', ['priority' => 1, 'status' => 1, 'title' => 'Home', 'slug' => 'home', 'path' => '/home', 'link' => '@page/1', 'type' => 'page', 'menu' => 'main', 'data' => "{\"defaults\":{\"id\":1}}"]);

App::db()->insert('@system_node', ['priority' => 2, 'status' => 1, 'title' => 'Blog', 'slug' => 'blog', 'path' => '/blog', 'link' => '@blog', 'type' => 'blog', 'menu' => 'main']);

App::db()->insert('@system_widget', ['title' => 'Hello, I\'m Pagekit', 'type' => 'system/text', 'status' => 1, 'nodes' => 1, 'data' => '{"content":"<h1 class=\"uk-heading-large uk-margin-large-bottom\">Hello, I\'m Pagekit,<br class=\"uk-hidden-small\"> your new favorite CMS.<\/h1>\n\n<a class=\"uk-button uk-button-large\" href=\"http:\/\/www.pagekit.com\">Get started<\/a>"}']);

App::db()->insert('@system_widget', ['title' => 'Powered by Pagekit', 'type' => 'system/text', 'status' => 1, 'data' => '{"content":"<ul class=\"uk-grid uk-grid-medium uk-flex uk-flex-center\">\n    <li><a href=\"https:\/\/github.com\/pagekit\" class=\"uk-icon-hover uk-icon-small uk-icon-github\"><\/a><\/li>\n    <li><a href=\"https:\/\/twitter.com\/pagekit\" class=\"uk-icon-hover uk-icon-small uk-icon-twitter\"><\/a><\/li>\n    <li><a href=\"https:\/\/gitter.im\/pagekit\/pagekit\" class=\"uk-icon-hover uk-icon-small uk-icon-comment-o\"><\/a><\/li>\n    <li><a href=\"https:\/\/plus.google.com\/communities\/104125443335488004107\" class=\"uk-icon-hover uk-icon-small uk-icon-google-plus\"><\/a><\/li>\n<\/ul>\n\n<p>Powered by <a href=\"https:\/\/pagekit.com\">Pagekit<\/a><\/p>"}']);

App::db()->insert('@system_page', [
    'title' => 'Home',
    'content' => "<div class=\"uk-width-medium-3-4 uk-container-center\">\n    \n<h3 class=\"uk-h1 uk-margin-large-bottom\">Uniting fresh design and clean code<br class=\"uk-hidden-small\"> to create beautiful websites.</h3>\n\n<p class=\"uk-width-medium-4-6 uk-container-center\">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</p>\n\n</div>",
    'data' => '{"title":true}'
]);

if (App::db()->getUtility()->tableExists('@blog_post')) {
    App::db()->insert('@blog_post', [
        'user_id' => 1,
        'slug' => 'hello-pagekit',
        'title' => 'Hello Pagekit',
        'status' => 2,
        'date' => date('Y-m-d H:i:s'),
        'modified' => date('Y-m-d H:i:s'),
        'content' => "Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\ntempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,\nquis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo\nconsequat. Duis aute irure dolor in reprehenderit in voluptate velit esse\ncillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non\nproident, sunt in culpa qui officia deserunt mollit anim id est laborum.",
        'excerpt' => '',
        'comment_status' => 1,
        'data' => '{"title":null,"markdown":true}'
    ]);
}
