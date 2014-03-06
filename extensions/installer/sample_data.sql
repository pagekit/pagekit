INSERT INTO `@system_menu` (`id`, `name`) VALUES
(1, 'Main');

INSERT INTO `@system_menu_item` (`id`, `menu_id`, `parent_id`, `access_id`, `name`, `url`, `priority`, `status`, `depth`, `data`) VALUES
(1, 1, 0, 1, 'Home', '@page\/id?id=1', 0, 1, 0, '[]');

INSERT INTO `@page_page` (`id`, `access_id`, `slug`, `title`, `status`, `content`) VALUES
(1, 1, 'home', 'Home', 1, 'Now you are ready to build awesome websites and benefit from the latest web technologies behind Pagekit. This is an alpha version, which means the system is still in development. Pagekit is hosted on GitHub and open for everyone to contribute. Please give us some feedback and join the development!\r\n\r\n## Additional Resources\r\n\r\n- [Pagekit Website](http://www.pagekit.com) - Learn more about Pagekit\r\n- [Pagekit on GitHub](https://github.com/pagekit/pagekit) - Join the development\r\n- [Pagekit Issues](https://github.com/pagekit/pagekit/issues?state=open) - Report bugs\r\n- [Pagekit on Twitter](https://twitter.com/pagekit) - Get the latest news\r\n- [Razr on GitHub](https://github.com/pagekit/razr) - Pagekit''s template engine\r\n- [UIkit Website](http://www.getuikit.com) - Pagekit''s front-end framework\r\n\r\nThe right place for developers to hang out and discuss Pagekit and its framework is on the IRC network `irc.freenode.net`. Just join the `#pagekit` channel.');

INSERT INTO `@system_widget` (`id`, `access_id`, `type`, `title`, `position`, `priority`, `status`, `pages`, `menu_items`, `data`) VALUES
(1, 1, 'widget.menu', 'Main Menu', 'navbar', 0, 1, '', NULL, '{"menu":"1","style":"list"}'),
(2, 1, 'widget.user.login', 'Login', 'sidebar', 0, 1, '', NULL, '{"redirect.login":"","redirect.logout":""}'),
(3, 1, 'widget.text', 'Copyright', 'footer', 0, 1, '', NULL, '{"content":"Powered by <a href=\\"\\">Pagekit<\\/a>"}');

INSERT INTO `@system_access_level` (`id`, `name`, `priority`, `roles`) VALUES
(3, 'Special', 2, '3');

INSERT INTO `@system_option` (`name`, `value`, `autoload`) VALUES
('system:app.frontpage', '"/page/1"', 1),
('system:extensions', '["page"]', 1);

UPDATE `@system_user` SET `data`='{\"dashboard\":{\"1\":{\"show\":\"login\",\"count\":\"5\",\"type\":\"widget.user\",\"widget\":{},\"widget.type\":{}},\"53183c730b5f4\":{\"location\":\"Hamburg, Germany\",\"id\":\"2827552\",\"units\":\"metric\",\"type\":\"widget.weather\",\"widget\":{},\"widget.type\":{}},\"53183c968c9e2\":{\"title\":\"YOOtheme\",\"url\":\"http:\\/\\/www.yootheme.com\\/rss\",\"count\":\"5\",\"content\":\"1\",\"type\":\"widget.feed\"}}}' WHERE `id`=1;
