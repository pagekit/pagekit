<?php

namespace Pagekit\Site\Model;

use Pagekit\Application as App;

abstract class Type implements TypeInterface
{
    protected $id;
    protected $label;
    protected $url;
    protected $options;

    public function __construct($id, $label, $url = '', array $options = [])
    {
        $this->id      = $id;
        $this->label   = $label;
        $this->url     = $url;
        $this->options = $options;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getLink(NodeInterface $node)
    {
        return $this->parseQuery($node->get('url', $this->getUrl()), $node->get('variables', []));
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
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
