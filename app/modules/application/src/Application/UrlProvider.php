<?php

namespace Pagekit\Application;

use Pagekit\Filesystem\Filesystem;
use Pagekit\Filesystem\Locator;
use Pagekit\Routing\Generator\UrlGenerator;
use Pagekit\Routing\Router;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class UrlProvider
{
    /**
     * Generates a path relative to the executed script, e.g. "/dir/file".
     */
    const BASE_PATH = 'base';

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var Filesystem
     */
    protected $file;

    /**
     * @var Locator
     */
    protected $locator;

    /**
     * Constructor.
     *
     * @param Router $router
     */
    public function __construct(Router $router, Filesystem $file, Locator $locator)
    {
        $this->router = $router;
        $this->file = $file;
        $this->locator = $locator;
    }

    /**
     * Get shortcut.
     *
     * @see get()
     */
    public function __invoke($path = '', $parameters = [], $referenceType = UrlGenerator::ABSOLUTE_PATH)
    {
        return $this->get($path, $parameters, $referenceType);
    }

    /**
     * Gets the base path for the current request.
     *
     * @param  mixed $referenceType
     * @return string
     */
    public function base($referenceType = UrlGenerator::ABSOLUTE_PATH)
    {
        $request = $this->router->getRequest();
        $url = $request->getBasePath();

        if ($referenceType === UrlGenerator::ABSOLUTE_URL) {
            $url = $request->getSchemeAndHttpHost().$url;
        } elseif ($referenceType === self::BASE_PATH) {
            $url = '';
        }

        return $url;
    }

    /**
     * Gets the URL for the current request.
     *
     * @param  mixed $referenceType
     * @return string
     */
    public function current($referenceType = UrlGenerator::ABSOLUTE_PATH)
    {
        $request = $this->router->getRequest();

        $url = $request->getBaseUrl();

        if ($referenceType === UrlGenerator::ABSOLUTE_URL) {
            $url = $request->getSchemeAndHttpHost().$url;
        }

        if ($qs = $request->getQueryString()) {
            $qs = '?'.$qs;
        }

        return $url.$request->getPathInfo().$qs;
    }

    /**
     * Gets the URL for the previous request.
     *
     * @return string
     */
    public function previous()
    {
        return $this->router->getRequest()->headers->get('referer');
    }

    /**
     * Gets the URL appending the URI to the base URI.
     *
     * @param  string $path
     * @param  mixed  $parameters
     * @param  mixed  $referenceType
     * @return string
     */
    public function get($path = '', $parameters = [], $referenceType = UrlGenerator::ABSOLUTE_PATH)
    {
        if (0 === strpos($path, '@')) {
            return $this->getRoute($path, $parameters, $referenceType);
        }

        $path = $this->parseQuery($path, $parameters);

        if (filter_var($path, FILTER_VALIDATE_URL) !== false) {
            return $path;
        }

        return $this->base($referenceType).'/'.ltrim($path, '/');
    }

    /**
     * Gets the URL to a named route.
     *
     * @param  string $name
     * @param  mixed  $parameters
     * @param  mixed  $referenceType
     * @return string|false
     */
    public function getRoute($name, $parameters = [], $referenceType = UrlGenerator::ABSOLUTE_PATH)
    {
        try {

            $url = $this->router->generate($name, $parameters, $referenceType === self::BASE_PATH ? UrlGenerator::ABSOLUTE_PATH : $referenceType);

            if ($referenceType === self::BASE_PATH) {
                $url = substr($url, strlen($this->router->getRequest()->getBaseUrl()));
            }

            return $url;

        } catch (RouteNotFoundException $e) {
        } catch (MissingMandatoryParametersException $e) {
        } catch (InvalidParameterException $e) {
        }

        return false;
    }

    /**
     * Gets the URL to a path resource.
     *
     * @param  string $path
     * @param  mixed  $parameters
     * @param  mixed  $referenceType
     * @return string
     */
    public function getStatic($path, $parameters = [], $referenceType = UrlGenerator::ABSOLUTE_PATH)
    {
        $url = $this->file->getUrl($this->locator->get($path) ?: $path, $referenceType === self::BASE_PATH ? UrlGenerator::ABSOLUTE_PATH : $referenceType);

        if ($referenceType === self::BASE_PATH) {
            $url = substr($url, strlen($this->router->getRequest()->getBasePath()));
        }

        return $this->parseQuery($url, $parameters);
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
            $url = strstr($url, '?', true);
            $parameters = array_replace($parameters, $params);
        }

        if ($query = http_build_query($parameters, '', '&')) {
            $url .= '?'.$query;
        }

        return $url;
    }
}
