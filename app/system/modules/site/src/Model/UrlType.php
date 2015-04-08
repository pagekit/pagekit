<?php

namespace Pagekit\Site\Model;

use Pagekit\Application as App;

class UrlType extends Type
{
    protected $url;

    public function __construct($id, $label, $url, array $options = [])
    {
        parent::__construct($id, $label, $options);

        $this->url = $url;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function bind(NodeInterface $node)
    {
        App::aliases()->add(
            $node->getPath(),
            $this->parseQuery($this->getUrl(), $node->get('variables', [])),
            array_merge($node->get('variables', []), $node->get('defaults', []))
        );
    }

    /**
     * Parses query parameters into a URL.
     *
     * @param  string $url
     * @param  array  $parameters
     * @return string
     */
    protected function parseQuery($url, $parameters = [])
    {
        if ($query = substr(strstr($url, '?'), 1)) {
            parse_str($query, $params);
            $url        = strstr($url, '?', true);
            $parameters = array_replace($parameters, $params);
        }

        if ($query = http_build_query($parameters, '', '&')) {
            $url .= '?'.$query;
        }

        return $url;
    }
}
