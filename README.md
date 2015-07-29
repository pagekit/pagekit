# Pagekit

[![Build Status](https://travis-ci.org/pagekit/pagekit.svg?branch=develop)](https://travis-ci.org/pagekit/pagekit)
[![Gitter](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/pagekit/pagekit)

Pagekit is a modular and lightweight CMS built with Symfony components.

* [Homepage](http://pagekit.com) - Learn more about Pagekit
* [@pagekit](https://twitter.com/pagekit) - Get the latest buzz on Twitter
* [Google+ Community](https://plus.google.com/communities/104125443335488004107) - Share news and latest work
* [Gitter Chat](https://gitter.im/pagekit/pagekit) - Join the developer chat on Gitter

## Getting started

Download the [latest release](http://www.pagekit.com) and extract the archive, then copy the extracted folder to your webserver. Create a database for Pagekit.
Run the Pagekit installation by accessing the URL where you uploaded the Pagekit files in a browser.

*Fresh \*.zip packages coming soon.*

## Install Pagekit from Source

Make sure you have the following tools installed: [Composer](https://getcomposer.org/doc/00-intro.md#installation-nix), [npm](https://www.npmjs.com/), [Bower](http://bower.io/), [Webpack](http://webpack.github.io/), [Gulp](http://gulpjs.com/).

Clone the repository.

```
git clone --branch develop git://github.com/pagekit/pagekit.git
```

Navigate to the cloned directory and install the PHP dependencies.

```
composer install
```

Install JavaScript dependencies.

```
npm install
```

Install frontend dependencies.

```
bower install
```

Compile the included LESS assets to CSS (or run `gulp watch` if you want to watch local file changes).

```
gulp
```

Bundle the included JS components (or run `webpack --watch` if you want to watch local file changes)

```
webpack
```

When these commands have finished, point your browser to the Pagekit URL on your web server and follow the installer.

## Stay up to date

If you've set up Pagekit from source, run these commands to get new commits and to rebuild everything you need.

```
git pull
composer update
npm install
bower update
gulp
webpack
```

## CLI

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
