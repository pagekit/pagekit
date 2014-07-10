# Pagekit

A modular and lightweight CMS built with Symfony components

* Homepage: [http://www.pagekit.com/](http://www.pagekit.com/) - Learn more about Pagekit
* Twitter: [@pagekit](https://twitter.com/pagekit) - Get the latest news
* IssueTracker: - [Issues](http://github.com/pagekit/pagekit/issues) - Report bugs here
* Developer Chat: #pagekit on irc.freenode.net


## Getting started

Download the [latest release](http://www.pagekit.com/download) and extract the archive, then copy the extracted folder to your webserver.
Create a database for Pagekit.
Run the Pagekit installation by accessing the URL where you uploaded the Pagekit files in a browser.

## Nginx
```
location / {
    try_files $uri $uri/ /index.php?$args;
}
```

## Developers

First of all, install [Composer](https://getcomposer.org/doc/00-intro.md#installation-nix).

If you haven't done so already, clone the Pagekit git repo.
```
git clone git://github.com/pagekit/pagekit.git
```

To install the dependencies of the project, navigate to the cloned directory and run the composer `install` command
```
composer install
#or if you don't have composer installed globally:
php path/to/composer.phar install
```

To update Pagekit, you need to pull the Pagekit git repo and run the composer `update` command

```
git pull
composer update
```

You may also clear the `app/cache` folder.

### CLI

Pagekit offers a set of commands to run usual tasks on the command line. You can see the available commands with
```
./pagekit --help
```
You can find further information about the command line tools in the [pagekit documentation](http://www.pagekit.com/documentation)


## Copyright and license

Copyright [YOOtheme](http://www.yootheme.com) GmbH under the [MIT license](LICENSE).
