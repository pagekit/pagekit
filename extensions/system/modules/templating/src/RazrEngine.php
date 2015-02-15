<?php

namespace Pagekit\Templating;

use Razr\Engine;
use Razr\Exception\InvalidArgumentException;
use Razr\Loader\LoaderInterface;
use Razr\Storage\Storage;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Templating\TemplateNameParserInterface;

class RazrEngine extends Engine implements EngineInterface
{
    /**
     * @var TemplateNameParserInterface
     */
    protected $nameParser;

    /**
     * @param TemplateNameParserInterface $nameParser
     * @param LoaderInterface             $loader
     * @param string                      $cachePath
     */
    public function __construct(TemplateNameParserInterface $nameParser, LoaderInterface $loader, $cachePath = null)
    {
        parent::__construct($loader, $cachePath);

        $this->nameParser = $nameParser;
    }

    /**
     * {@inheritdoc}
     */
    public function exists($name)
    {
        try {
            $this->load($name);
        } catch (InvalidArgumentException $e) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($name)
    {
        $template = $this->nameParser->parse($name);

        return 'razr' === $template->get('engine');
    }

    /**
     * Loads a template by name.
     *
     * @param  string $name
     * @return Storage
     */
    public function loadTemplate($name)
    {
        return $this->load($name);
    }

    /**
     * {@inheritdoc}
     */
    protected function load($name)
    {
        $template = $this->nameParser->parse($name);

        if (!file_exists($path = $template->getPath())) {
            throw new InvalidArgumentException(sprintf('The template "%s" does not exist.', $name));
        }

        return parent::load($path);
    }
}
