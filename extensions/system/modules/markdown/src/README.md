Markdown
========

PHP Markdown parser and compiler.


### Usage


```php

use Pagekit\Markdown\Markdown;

$options = array(
    'gfm'         => true,
    'tables'      => true,
    'breaks'      => false,
    'pedantic'    => false,
    'sanitize'    => true,
    'smartLists'  => true,
    'smartypants' => false
);

$markdown = new Markdown($options);
$markdown->render('**markdown**');
```


## Credits

The Markdown parser code is based on [marked](https://github.com/chjj/marked) by [Christopher Jeffrey (MIT License)](https://github.com/chjj/marked/blob/master/LICENSE).
