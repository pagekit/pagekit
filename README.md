# Pagekit

[![Build Status](https://travis-ci.org/pagekit/pagekit.svg?branch=develop)](https://travis-ci.org/pagekit/pagekit)
[![Gitter](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/pagekit/pagekit)

Pagekit is a modular and lightweight CMS built with Symfony components.

* [Homepage](http://pagekit.com) - Learn more about Pagekit
* [@pagekit](https://twitter.com/pagekit) - Get the latest buzz on Twitter
* [Google+ Community](https://plus.google.com/communities/104125443335488004107) - Share news and latest work
* [Gitter Chat](https://gitter.im/pagekit/pagekit) - Join the developer chat on Gitter

## Getting started

Download the [latest release](http://www.pagekit.com) and extract the archive, then copy the extracted folder to your webserver.
Create a database for Pagekit.
Run the Pagekit installation by accessing the URL where you uploaded the Pagekit files in a browser.

## Developers

**The `develop` version is undergoing heavy work at the moment. We invite you to join the fun 
and get the current version running on your system. Be aware that those versions are bleeding
edge. They can and will break from one day to the next and are not suited for use in production.**

First of all, install [Composer](https://getcomposer.org/doc/00-intro.md#installation-nix).

If you haven't done so already, clone the Pagekit git repo.
```
git clone --branch develop git://github.com/pagekit/pagekit.git
```

To install the PHP dependencies of the project, navigate to the cloned directory and run the composer `install` command
```
composer install
#or if you don't have composer installed globally:
php path/to/composer.phar install
```

Pagekit use [Bower](http://bower.io/) and [npm](https://www.npmjs.com/) for front-end and JavaScript dependencies.

```
bower install
npm install
```

Pagekit uses [Gulp](http://gulpjs.com/) to compile the included LESS assets to CSS.

```
gulp compile
```

When these commands have finished, point your browser to the Pagekit URL on your web server and follow the installer.

To update Pagekit, you need to pull the Pagekit git repo and run the composer `update` command

```
git pull
composer update
```

You may also clear the `tmp/cache` folder.

### CLI

Pagekit offers a set of commands to run usual tasks on the command line. You can see the available commands with
```
./pagekit --help
```
You can find further information about the command line tools in the [pagekit documentation](http://www.pagekit.com/docs/quickstart)

## Contributing

Pagekit follows the [GitFlow branching model](http://nvie.com/posts/a-successful-git-branching-model). The ```master``` branch always reflects a production-ready state while the latest development is taking place in the ```develop``` branch.

Each time you want to work on a fix or a new feature, create a new branch based on the ```develop``` branch: ```git checkout -b BRANCH_NAME develop```. Only pull requests to the ```develop``` branch will be merged.

## Versioning

Pagekit is maintained by using the [Semantic Versioning Specification (SemVer)](http://semver.org).

## Copyright and License

Copyright [YOOtheme](http://www.yootheme.com) GmbH under the [MIT license](LICENSE.md).
