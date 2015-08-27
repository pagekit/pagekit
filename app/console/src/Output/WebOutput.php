<?php

namespace Pagekit\Console\Output;

use Composer\Console\HtmlOutputFormatter;

class WebOutput extends FilterOutput
{
    public function __construct($stream)
    {
        parent::__construct($stream);

        $this->setFormatter(new HtmlOutputFormatter());

        header('Content-type: text/html; charset=utf-8');

        ob_implicit_flush(true);
        ob_end_flush();

        register_shutdown_function(function () {
            echo sprintf('status=%s', $this->getError() ? 'error' : 'success');
        });
    }
}
