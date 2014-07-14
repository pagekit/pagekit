<?php

namespace Pagekit\Comment\Helper;

use Pagekit\Comment\Model\Node;

class CommentHelper
{
    /**
     * Retrieves comments tree.
     *
     * @param  array $comments
     * @param  array $parameters
     * @return Node
     */
    public function getTree($comments = [], array $parameters = [])
    {
        $nodes = [new Node(0)];

        foreach ($comments as $comment) {
            $id   = $comment->getId();
            $pid  = $comment->getParentId();

            if (!isset($nodes[$id])) {
                $nodes[$id] = new Node($id);
            }

            $nodes[$id]->setComment($comment);

            if (!isset($nodes[$pid])) {
                $nodes[$pid] = new Node($pid);
            }

            $nodes[$pid]->add($nodes[$id]);
        }

        return $nodes[isset($parameters['root'], $nodes[$parameters['root']]) ? $parameters['root'] : 0];
    }


    /**
     * Remove html from comment content
     *
     * @param  string $content
     * @return string
     */
    public function filterContentInput($content)
    {
        // remove all html tags or escape if in [code] tag
        $content = preg_replace_callback('/\[code\](.+?)\[\/code\]/is', function($matches) { return htmlspecialchars($matches[0]); }, $content);
        $content = strip_tags($content);

        return $content;
    }

    /**
     * Auto linkify urls, emails
     *
     * @param  string $content
     * @return string
     */
    public function filterContentOutput($content)
    {
        $content = ' '.$content.' ';
        $content = preg_replace_callback('/(?:(?:https?|ftp|file):\/\/|www\.|ftp\.)(?:\([-A-Z0-9+&@#\/%=~_|$?!:;,.]*\)|[-A-Z0-9+&@#\/%=~_|$?!:;,.])*(?:\([-A-Z0-9+&@#\/%=~_|$?!:;,.]*\)|[A-Z0-9+&@#\/%=~_|$])/ix', function ($matches) {

            $url = $original_url = $matches[0];

            if (empty($url)) {
                return $url;
            }

            // Prepend scheme if URL appears to contain no scheme (unless a relative link starting with / or a php file).
            if (strpos($url, ':') === false &&	substr($url, 0, 1) != '/' && substr($url, 0, 1) != '#' && !preg_match('/^[a-z0-9-]+?\.php/i', $url)) {
                $url = 'http://' . $url;
            }

            return " <a href=\"$url\" rel=\"nofollow\">$original_url</a>";
        }, $content);
        $content = preg_replace("/\s([a-zA-Z][a-zA-Z0-9\_\.\-]*[a-zA-Z]*\@[a-zA-Z][a-zA-Z0-9\_\.\-]*[a-zA-Z]{2,6})([\s|\.|\,])/i"," <a href=\"mailto:$1\" rel=\"nofollow\">$1</a>$2", $content);
        $content = substr($content, 1);
        $content = substr($content, 0, -1);

        return nl2br($content);
    }
} 