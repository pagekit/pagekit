INSERT INTO `@system_menu` (`id`, `name`) VALUES
(1, 'Main');

INSERT INTO `@system_menu_item` (`id`, `menu_id`, `parent_id`, `roles`, `name`, `url`, `priority`, `status`, `depth`, `pages`, `data`) VALUES
(1, 1, 0, NULL, 'Home', '/', 0, 1, 0, '', '[]');

INSERT INTO `@page_page` (`id`, `roles`, `url`, `title`, `status`, `content`, `data`) VALUES
(1, NULL, 'home', 'Home', 1, 'Now you are ready to build awesome websites and benefit from the latest web technologies behind Pagekit. This is an alpha version, which means the system is still in development.

You can find [Pagekit on GitHub](https://github.com/pagekit/pagekit) and it is open for everyone to contribute. Please [report bugs](https://github.com/pagekit/pagekit/issues?state=open) and send us pull requests. We are looking forward to your feedback and ideas!

We also created a first draft of the [documentation on GitHub](https://github.com/pagekit/docs) with a lot of useful information on building themes and extensions. Please let us know, if something is missing or if you found an error.

Should something not work, don''t hesitate to ask in [our chat](https://www.hipchat.com/giPcIKmrx). We really appreciate your feedback. We are usually online between 8:00 and 18:00 UTC

## Additional Resources

- [Pagekit on Twitter](https://twitter.com/pagekit) - Get the latest news
- [Razr on GitHub](https://github.com/pagekit/razr) - Pagekit''s template engine
- [UIkit Website](http://www.getuikit.com) - Pagekit''s front-end framework

Enjoy developing with Pagekit!', '{"markdown":"1"}');

INSERT INTO `@system_widget` (`id`, `roles`, `type`, `title`, `position`, `priority`, `status`, `pages`, `menu_items`, `data`) VALUES
(1, NULL, 'widget.menu', 'Main Menu', 'navbar', 0, 1, '', NULL, '{"menu":"1","style":"list"}');

INSERT INTO `@system_widget` (`id`, `roles`, `type`, `title`, `position`, `priority`, `status`, `pages`, `menu_items`, `data`) VALUES
(2, NULL, 'widget.user.login', 'Login', 'sidebar', 0, 1, '', NULL, '{"redirect.login":"","redirect.logout":""}');

INSERT INTO `@system_widget` (`id`, `roles`, `type`, `title`, `position`, `priority`, `status`, `pages`, `menu_items`, `data`) VALUES
(3, NULL, 'widget.text', 'Copyright', 'footer', 0, 1, '', NULL, '{"content":"Powered by <a href=\\"\\">Pagekit<\\/a>"}');

INSERT INTO `@system_option` (`name`, `value`, `autoload`) VALUES
('system:app.frontpage', '"@page/id?id=1"', 1);

UPDATE `@system_user` SET `data`='{\"dashboard\":{\"1\":{\"show\":\"login\",\"count\":\"5\",\"type\":\"widget.user\",\"widget\":{},\"widget.type\":{}},\"53183c730b5f4\":{\"location\":\"Hamburg, Germany\",\"id\":\"2827552\",\"units\":\"metric\",\"type\":\"widget.weather\",\"widget\":{},\"widget.type\":{}},\"53183c968c9e2\":{\"title\":\"Pagekit\",\"url\":\"http:\\/\\/pagekit.com\\/blog\\/feed\",\"count\":\"5\",\"content\":\"1\",\"type\":\"widget.feed\"}}}' WHERE `id`=1;
