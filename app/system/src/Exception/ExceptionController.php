<?php

namespace Pagekit\System\Exception;

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
        $currentContent = $this->getAndCleanOutputBuffering($request->headers->get('X-Php-Ob-Level', -1));

        if (is_subclass_of($exception->getClass(), 'Symfony\Component\HttpKernel\Exception\HttpExceptionInterface')) {
            $title = $exception->getMessage();
        } else {
            $title = __('Whoops, looks like something went wrong.');
        }

        $response = App::view('app/system/modules/theme/templates/error.php', compact('title', 'exception', 'currentContent'));

        return App::response($response, $exception->getStatusCode(), $exception->getHeaders());
    }

    /**
     * @param int $startObLevel
     *
     * @return string
     */
    protected function getAndCleanOutputBuffering($startObLevel)
    {
        if (ob_get_level() <= $startObLevel) {
            return '';
        }

        Response::closeOutputBuffers($startObLevel + 1, true);

        return ob_get_clean();
    }
}
