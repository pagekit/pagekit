<?php

namespace Pagekit\System\Exception;

use Pagekit\Framework\ApplicationTrait;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;

class ExceptionController implements \ArrayAccess
{
    use ApplicationTrait;

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

        switch ($exception->getClass()) {
            case 'Pagekit\Component\Session\Csrf\Exception\BadTokenException':
                $title = __('Invalid CSRF token.');
                break;
            case 'Symfony\Component\HttpKernel\Exception\NotFoundHttpException':
                $title = __('Sorry, the page you are looking for could not be found.');
                break;
            case 'Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException':
                $title = $exception->getMessage();
                break;
            default:
                $title = __('Whoops, looks like something went wrong.');
        }

        $response = $this['view']->render('extension://system/theme/templates/error.razr', compact('title', 'exception', 'currentContent'));

        return $this['response']->create($response, $exception->getStatusCode(), $exception->getHeaders());
    }

    /**
     * @param int     $startObLevel
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
