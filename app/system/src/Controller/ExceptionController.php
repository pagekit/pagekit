<?php

namespace Pagekit\System\Controller;

use Pagekit\Application as App;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ExceptionController
{
    /**
     * Converts an Exception to a Response.
     *
     * @param  Request          $request
     * @param  FlattenException $exception
     * @return Response
     */
    public function showAction(Request $request, FlattenException $exception)
    {
        if (is_subclass_of($exception->getClass(), 'Pagekit\Kernel\Exception\HttpException')) {
            $title = $exception->getMessage();
        } else {
            $title = __('Whoops, looks like something went wrong.');
        }

        $content  = $this->getAndCleanOutputBuffering($request->headers->get('X-Php-Ob-Level', -1));
        $response = App::view('system/error.php', compact('title', 'exception', 'content'));

        return App::response($response, $exception->getCode(), $exception->getHeaders());
    }

    /**
     * Cleans output buffer.
     *
     * @param  int    $level
     * @return string
     */
    protected function getAndCleanOutputBuffering($level)
    {
        if (ob_get_level() <= $level) {
            return '';
        }

        Response::closeOutputBuffers($level + 1, true);

        return ob_get_clean();
    }
}
