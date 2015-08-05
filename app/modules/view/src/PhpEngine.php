<?php

namespace Pagekit\View;

use Symfony\Component\Templating\Loader\FilesystemLoader;
use Symfony\Component\Templating\Loader\LoaderInterface;
use Symfony\Component\Templating\PhpEngine as BasePhpEngine;
use Symfony\Component\Templating\Storage\FileStorage;
use Symfony\Component\Templating\Storage\Storage;
use Symfony\Component\Templating\Storage\StringStorage;
use Symfony\Component\Templating\TemplateNameParser;
use Symfony\Component\Templating\TemplateNameParserInterface;

class PhpEngine extends BasePhpEngine
{
    protected $template;
    protected $parameters;

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
        $this->template = $template;
        $this->parameters = $parameters;
        unset($template, $parameters);

        if (isset($this->parameters['this'])) {
            throw new \InvalidArgumentException('Invalid parameter (this)');
        }

        if ($this->template instanceof FileStorage) {
            extract($this->parameters, EXTR_SKIP);
            $this->parameters = null;

            ob_start();
            require $this->template;

            $this->template = null;

            return ob_get_clean();
        } elseif ($this->template instanceof StringStorage) {
            extract($this->parameters, EXTR_SKIP);
            $this->parameters = null;

            ob_start();
            eval('; ?>'.$this->template.'<?php ;');

            $this->template = null;

            return ob_get_clean();
        }

        return false;
    }
}
