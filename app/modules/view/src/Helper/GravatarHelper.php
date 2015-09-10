<?php

namespace Pagekit\View\Helper;

class GravatarHelper extends Helper
{
    /**
     * Get either a Gravatar URL or complete image tag for a specified email address.
     *
     * @param  string $email  The email to be used for fetching the gravatar
     * @param  array  $params Parameter array as follows:
     *                        size    => Size in pixels, defaults to 80px [ 1 - 2048 ]
     *                        default => Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
     *                        rating  => Maximum rating (inclusive) [ g | pg | r | x ]
     *                        img     => True to return a complete IMG tag False for just the URL
     *                        attrs   => Optional, additional key/value attributes to include in the IMG tag (array)
     * @return string         Generated gravatar string (url or <img>)
     */
    public function __invoke($email, $params = [])
    {
        $params = array_merge([
            'size'    => 80,
            'default' => 'mm',
            'rating'  => 'g',
            'img'     => true,
            'attrs'   => []
        ], $params);

        $url = sprintf('//gravatar.com/avatar/%s?s=%s&d=%s&r=%s', md5(strtolower(trim($email))), $params['size'], $params['default'], $params['rating']);

        if ($params['img']) {

            $attrs = array_merge([
                'src'    => $url,
                'width'  => $params['size'],
                'height' => $params['size']
            ], $params['attrs']);

            $attrs = array_map(function($name, $value) {
                return sprintf('%s="%s"', $name, htmlspecialchars($value));
            }, array_keys($attrs), $attrs);

            return '<img '.implode(' ', $attrs).'/>';
        }

        return $url;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'gravatar';
    }
}
