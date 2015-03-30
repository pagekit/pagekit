<?php

namespace Pagekit\Application;

use Pagekit\Application as App;
use Symfony\Component\HttpFoundation\RedirectResponse;

class Controller
{
    /**
     * Returns a redirect response.
     *
     * @param  string  $url
     * @param  array   $parameters
     * @param  int     $status
     * @param  array   $headers
     * @return RedirectResponse
     */
    public function redirect($url = '', $parameters = [], $status = 302, $headers = [])
    {
        return new RedirectResponse(App::url($url, $parameters), $status, $headers);
    }
}
