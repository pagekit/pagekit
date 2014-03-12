<?php

namespace Pagekit\System\Exception;

use Pagekit\Component\View\Csrf\Exception\BadTokenException;
use Pagekit\Framework\ApplicationAware;
use Pagekit\Framework\Exception\ExceptionHandlerInterface;
use Symfony\Component\Debug\Exception\FlattenException;

class ExceptionHandler extends ApplicationAware implements ExceptionHandlerInterface
{
    /**
     * @var bool
     */
    protected $debug;

    /**
     * Constructor.
     *
     * @param bool $debug
     */
    public function __construct($debug = true)
    {
        $this->debug = $debug;
    }

    /**
     * @{inheritdoc}
     */
    public function handle(\Exception $exception)
    {
        if ($this->debug) {
            return false;
        }

        if ($exception instanceof BadTokenException) {
            $title = __('Invalid CSRF token.');
        } elseif ($exception->getStatusCode() == 404) {
            $title = __('Sorry, the page you are looking for could not be found.');
        } else {
            $title = __('Whoops, looks like something went wrong.');
        }

        if (!$exception instanceof FlattenException) {
            $exception = FlattenException::create($exception);
        }

        $response = $this('view')->render('extension://system/theme/templates/error.razr.php', compact('title', 'exception'));

        $this('response')->create($response, $exception->getStatusCode(), $exception->getHeaders())->send();

        return true;
    }
}
