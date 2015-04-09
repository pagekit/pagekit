<?php

namespace Pagekit\System\Controller;

use Pagekit\Application as App;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;

class ExceptionController
{
    /**
     * Converts an Exception to a Response.
     *
     * @param  Request              $request
     * @param  FlattenException     $exception
     * @param  DebugLoggerInterface $logger
     * @param  string               $_format
     * @throws \InvalidArgumentException
     * @return Response
     */
    public function showAction(Request $request, FlattenException $exception, DebugLoggerInterface $logger = null, $_format = 'html')
    {
        if (is_subclass_of($exception->getClass(), 'Symfony\Component\HttpKernel\Exception\HttpExceptionInterface')) {
            $title = $exception->getMessage();
        } else {
            $title = __('Whoops, looks like something went wrong.');
        }

        $content  = $this->getAndCleanOutputBuffering($request->headers->get('X-Php-Ob-Level', -1));
        $response = App::view('system/theme:templates/error.php', compact('title', 'exception', 'content'));

        return App::response($response, $exception->getStatusCode(), $exception->getHeaders());
    }

    /**
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
