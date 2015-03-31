<?php

namespace Pagekit\View;

use Symfony\Component\Templating\PhpEngine as BasePhpEngine;
use Symfony\Component\Templating\TemplateNameParser;
use Symfony\Component\Templating\TemplateNameParserInterface;
use Symfony\Component\Templating\Loader\LoaderInterface;
use Symfony\Component\Templating\Loader\FilesystemLoader;
use Symfony\Component\Templating\Storage\FileStorage;
use Symfony\Component\Templating\Storage\Storage;
use Symfony\Component\Templating\Storage\StringStorage;

class PhpEngine extends BasePhpEngine
{
    /**
     * {@inheritdoc}
     */
    public function __construct(TemplateNameParserInterface $parser = null, LoaderInterface $loader = null, array $helpers = array())
    {
        $parser = $parser ?: new TemplateNameParser();
        $loader = $loader ?: new FilesystemLoader([]);

        parent::__construct($parser, $loader, $helpers);
    }

    /**
     * {@inheritdoc}
     */
    protected function evaluate(Storage $template, array $parameters = array())
    {
        $this->evalTemplate = $template;
        $this->evalParameters = $parameters;
        unset($template, $parameters);

        if (isset($this->evalParameters['this'])) {
            throw new \InvalidArgumentException('Invalid parameter (this)');
        }

        if ($this->evalTemplate instanceof FileStorage) {
            extract($this->evalParameters, EXTR_SKIP);
            $this->evalParameters = null;

            ob_start();
            require $this->evalTemplate;

            $this->evalTemplate = null;

            return ob_get_clean();
        } elseif ($this->evalTemplate instanceof StringStorage) {
            extract($this->evalParameters, EXTR_SKIP);
            $this->evalParameters = null;

            ob_start();
            eval('; ?>'.$this->evalTemplate.'<?php ;');

            $this->evalTemplate = null;

            return ob_get_clean();
        }

        return false;
    }
}
