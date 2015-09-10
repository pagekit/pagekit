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
    protected $result;
    protected $template;
    protected $parameters;

    /**
     * {@inheritdoc}
     */
    public function __construct(TemplateNameParserInterface $parser = null, LoaderInterface $loader = null, array $helpers = [])
    {
        $parser = $parser ?: new TemplateNameParser();
        $loader = $loader ?: new FilesystemLoader([]);

        parent::__construct($parser, $loader, $helpers);
    }

    /**
     * {@inheritdoc}
     */
    protected function evaluate(Storage $template, array $parameters = [])
    {
        $this->result = false;
        $this->template = $template;
        $this->parameters = $parameters;

        unset($template, $parameters);

        if (isset($this->parameters['this'])) {
            throw new \InvalidArgumentException('Invalid parameter (this)');
        }

        extract($this->parameters, EXTR_SKIP);

        if ($this->template instanceof FileStorage) {
            ob_start();
            require $this->template;
            $this->result = ob_get_clean();
        } elseif ($this->template instanceof StringStorage) {
            ob_start();
            eval('; ?>'.$this->template.'<?php ;');
            $this->result = ob_get_clean();
        }

        return $this->result;
    }
}
